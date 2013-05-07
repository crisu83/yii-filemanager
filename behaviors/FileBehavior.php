<?php
/**
 * FileBehavior class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.behaviors
 */

/**
 * File behavior for active records that are associated with files.
 */
class FileBehavior extends CActiveRecordBehavior
{
	/**
	 * @var string the name of the model attribute that holds the file id (defaults to 'fileId').
	 */
	public $fileIdAttribute = 'fileId';
	/**
	 * @var string the component id for the file manager component (defaults to 'fileManager').
	 */
	public $componentID = 'fileManager';

	/** @var FileManager */
	private $_fileManager;

	/**
	 * Saves the given file both in the database and on the hard disk.
	 * @param CUploadedFile $file the uploaded file.
	 * @param string $name the new name for the file.
	 * @param string $path the path relative to the base path.
	 * @return File the model.
	 * @see FileManager::saveFile
	 */
	public function saveFile($file, $name = null, $path = null)
	{
		return $this->getFileManager()->saveFile($file, $name, $path);
	}

	/**
	 * Returns the file with the given id.
	 * @return File the model.
	 * @see FileManager::loadFile
	 */
	public function loadFile()
	{
		$id = $this->owner->{$this->fileIdAttribute};
		return $this->getFileManager()->loadFile($id);
	}

	/**
	 * Returns the relative url for the given model.
	 * @param File $model the file model.
	 * @return string the url.
	 * @see FileManager::resolveFilePath
	 */
	public function resolveFilePath()
	{
		$model = $this->loadFile();
		return $this->getFileManager()->resolveFilePath($model);
	}

	/**
	 * Returns the full path for the given model.
	 * @param File $model the file model.
	 * @return string the path.
	 * @see FileManager::resolveFileUrl
	 */
	public function resolveFileUrl()
	{
		$model = $this->loadFile();
		return $this->getFileManager()->resolveFileUrl($model);
	}

	/**
	 * Returns the file manager application component.
	 * @return FileManager the component.
	 */
	public function getFileManager()
	{
		if (isset($this->_fileManager))
			return $this->_fileManager;
		else
			return $this->_fileManager = Yii::app()->getComponent($this->componentID);
	}
}
