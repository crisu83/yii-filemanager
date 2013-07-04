<?php
/**
 * FileManager class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.components
 */

Yii::import('vendor.crisu83.yii-extension.behaviors.ComponentBehavior');

/**
 * Application component for managing files.
 */
class FileManager extends CApplicationComponent
{
	/**
	 * @var string the path for storing the files.
	 */
	public $filePath = 'webroot.files';

	private $_basePath;

	/**
	 * Initializes the component.
	 */
	public function init()
	{
		parent::init();
		$this->attachBehavior('ext', new ComponentBehavior);
		$this->createPathAlias('fileManager', __DIR__ . DIRECTORY_SEPARATOR . '..');
		$this->import('models.*');
	}

	/**
	 * Saves the given file both in the database and on the hard disk.
	 * @param CUploadedFile $file the uploaded file.
	 * @param string $name the new name for the file.
	 * @param string $path the path relative to the base path.
	 * @throws CException if saving the image fails.
	 * @return File the model.
	 */
	public function save($file, $name = null, $path = null)
	{
		if (!$file instanceof CUploadedFile)
			throw new CException('Failed to save file. File is not an instance of CUploadedFile.');
		/* @var CDbConnection $db */
		$db = $this->getDbConnection();
		$trx = $db->beginTransaction();
		try {
			$model = new File;
			$model->extension = strtolower($file->getExtensionName());
			$model->filename = $file->getName();
			$model->mimeType = $file->getType();
			$model->byteSize = $file->getSize();
			$model->createdAt = date('Y-m-d H:i:s');
			if ($name === null)
			{
				$filename = $model->filename;
				$name = substr($filename, 0, strrpos($filename, '.'));
			}
			$model->name = $this->normalizeFilename($name);
			if ($path !== null)
				$model->path = trim($path, '/');
			if ($model->save() === false)
				throw new CException('Failed to save file. Database record could not be saved.');
			$filePath = $this->getBasePath() . $model->resolvePath();
			if (!file_exists($filePath) && !$this->createDirectory($filePath))
				throw new CException('Failed to save file. Directory could not be created.');
			$filePath .= $model->resolveFilename();
			if ($file->saveAs($filePath) === false)
				throw new CException('Failed to save file. File could not be saved.');
			$trx->commit();
			return $model;
		} catch (CException $e) {
			$trx->rollback();
			throw $e;
		}
	}

	/**
	 * Returns the file with the given id.
	 * @param integer $id the model id.
	 * @return File the model.
	 * @throws CException if loading the file fails.
	 */
	public function load($id)
	{
		$model = File::model()->findByPk($id);
		if ($model === null)
			throw new CException('Failed to load file. Database record not found.');
		return $model;
	}

	/**
	 * Deletes a file with the given id.
	 * @param integer $id the file id.
	 * @return boolean whether the file was successfully deleted.
	 * @throws CException if deleting the file fails.
	 */
	public function delete($id)
	{
		$model = $this->load($id);
		$filePath = $model->resolveFilePath();
		if (file_exists($filePath) !== false && unlink($filePath) === false)
			throw new CException('Failed to delete file. File could not be deleted.');
		if ($model->delete() === false)
			throw new CException('Failed to delete file. Database record could not be deleted.');
		return true;
	}

	/**
	 * @param File $model
	 * @return string
	 */
	public function resolvePathForFile($model)
	{
		return $this->getBasePath() . $model->resolveFilePath();
	}

	/**
	 * Returns the path for storing files.
	 * @return string the path.
	 */
	public function getBasePath()
	{
		if (isset($this->_basePath))
			return $this->_basePath;
		else
			return $this->_basePath = Yii::getPathOfAlias($this->filePath) . '/';
	}

	/**
	 * Creates a directory.
	 * @param string $path the directory path.
	 * @param integer $mode the chmod mode (ignored on windows).
	 * @param boolean $recursive whether to create the directory recursively.
	 * @return boolean whether the directory was created.
	 */
	protected function createDirectory($path, $mode = 0777, $recursive = true)
	{
		return mkdir($path, $mode, $recursive);
	}

	/**
	 * Normalizes the given filename by removing illegal characters.
	 * @param string $name the filename.
	 * @return string the normalized filename.
	 */
	protected function normalizeFilename($name)
	{
		$name = str_replace(str_split('/\?%*:|"<>'), '', $name);
		return str_replace(' ', '-', $name); // for convenience
	}
}