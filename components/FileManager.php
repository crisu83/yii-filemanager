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
     * @var string name of the files directory.
     */
    public $fileDir = 'files';
    /**
     * @var string the base path (defaults to 'webroot').
     */
    public $basePath = 'webroot';
    /**
     * @var string the base url. If omitted the base url for the request will be used.
     */
    public $baseUrl;
    /**
     * @var string the name of the file model class.
     */
    public $modelClass = 'File';

    private $_basePath;
    private $_baseUrl;

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
    public function saveModel($file, $name = null, $path = null)
    {
        if (!$file instanceof CUploadedFile)
            throw new CException('Failed to save file. File is not an instance of CUploadedFile.');
        /* @var File $model */
        $model = new $this->modelClass;
        $model->setManager($this);
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
        if (!$model->save())
            throw new CException('Failed to save file. Database record could not be saved.');
        $filePath = $model->resolvePath();
        if (!file_exists($filePath) && !$this->createDirectory($filePath))
            throw new CException('Failed to save file. Directory could not be created.');
        $filePath .= $model->resolveFilename();
        if (!$file->saveAs($filePath))
            throw new CException('Failed to save file. File could not be saved.');
        return $model;
    }

    /**
     * Returns the file with the given id.
     * @param integer $id the model id.
     * @return File the model.
     * @throws CException if loading the file fails.
     */
    public function loadModel($id)
    {
        /* @var File $model */
        $model = File::model()->findByPk($id);
        if ($model === null)
            throw new CException('Failed to load file. Database record not found.');
        $model->setManager($this);
        return $model;
    }

    /**
     * Deletes a file with the given id.
     * @param integer $id the file id.
     * @return boolean whether the file was successfully deleted.
     * @throws CException if deleting the file fails.
     */
    public function deleteModel($id)
    {
        $model = $this->loadModel($id);
        $filePath = $model->resolvePath();
        if (file_exists($filePath) && !unlink($filePath))
            throw new CException('Failed to delete file. File could not be deleted.');
        if (!$model->delete())
            throw new CException('Failed to delete file. Database record could not be deleted.');
        return true;
    }

    /**
     * Returns the url to the files directory.
     * @return string the url.
     */
    public function getBaseUrl()
    {
        if (isset($this->_baseUrl))
            return $this->_baseUrl;
        else
        {
            $baseUrl = isset($this->baseUrl) ? $this->baseUrl : Yii::app()->request->baseUrl;
            return $this->_baseUrl = $baseUrl . '/' . $this->fileDir . '/';
        }
    }

    /**
     * Returns the path to the files directory.
     * @return string the path.
     */
    public function getBasePath()
    {
        if (isset($this->_basePath))
            return $this->_basePath;
        else
            return $this->_basePath = Yii::getPathOfAlias($this->basePath) . '/' . $this->fileDir . '/';
    }

    /**
     * Creates a directory.
     * @param string $path the directory path.
     * @param integer $mode the chmod mode (ignored on windows).
     * @param boolean $recursive whether to create the directory recursively.
     * @return boolean whether the directory was created or already exists.
     */
    public function createDirectory($path, $mode = 0777, $recursive = true)
    {
        return !file_exists($path) ? mkdir($path, $mode, $recursive) : true;
    }

    /**
     * Deletes a directory.
     * @param string $path the directory path.
     * @return boolean whether the directory was deleted.
     */
    public function deleteDirectory($path)
    {
        return file_exists($path) ? unlink($path) : false;
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