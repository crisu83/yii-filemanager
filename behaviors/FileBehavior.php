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
 *
 * @property CActiveRecord $owner
 */
class FileBehavior extends CActiveRecordBehavior
{
    /**
     * @var string name to save the file with.
     */
    public $name;

    /**
     * @var string internal path for saving the file.
     */
    public $path;

    /**
     * @var string name of the model attribute that holds the file id (defaults to 'fileId').
     */
    public $idAttribute = 'fileId';

    /**
     * @var string name of the model attribute that holds the uploaded file (defaults to 'upload').
     */
    public $uploadAttribute = 'upload';

    /**
     * @var boolean whether to save the file automatically when saving the owner model.
     */
    public $autoSave = true;

    /**
     * @var boolean whether to delete the file automatically when deleting the owner model.
     */
    public $autoDelete = true;

    /**
     * @var string the component id for the file manager component (defaults to 'fileManager').
     */
    public $managerID = 'fileManager';

    /**
     * Actions to take before validating the owner of this behavior.
     * @param CModelEvent $event event parameter.
     */
    protected function beforeValidate($event)
    {
        if ($this->autoSave) {
            $this->owner->{$this->uploadAttribute} = $this->getUploadedFile();
        }
    }

    /**
     * Actions to take before saving the owner of this behavior.
     * @param CModelEvent $event event parameter.
     */
    protected function beforeSave($event)
    {
        if ($this->autoSave) {
            $this->saveFile($this->owner->{$this->uploadAttribute}, $this->name, $this->path);
        }
    }

    /**
     * Actions to take before deleting the owner of this behavior.
     * @param CModelEvent $event event parameter.
     */
    protected function beforeDelete($event)
    {
        if ($this->autoDelete) {
            $this->deleteFile();
        }
    }

    /**
     * Returns the uploaded image file.
     * @return CUploadedFile the file.
     */
    public function getUploadedFile()
    {
        return CUploadedFile::getInstance($this->owner, $this->uploadAttribute);
    }

    /**
     * Saves the given file both in the database and on the hard disk.
     * @param CUploadedFile $file the uploaded file.
     * @param string $name new name for the file.
     * @param string $path path relative to the base path.
     * @param string $scenario name of the scenario.
     * @return File the model.
     * @see FileManager::saveModel
     */
    public function saveFile($file, $name = null, $path = null, $scenario = 'insert')
    {
        $model = $this->getManager()->saveModel(new UploadedFile($file), $name, $path, $scenario);
        $this->owner->{$this->idAttribute} = $model->id;
        return $model;
    }

    /**
     * Returns the file with the given id.
     * @param array $with related models that should be eager-loaded.
     * @return File the model.
     * @see FileManager::loadModel
     */
    public function loadFile($with = array())
    {
        return $this->getManager()->loadModel($this->owner->{$this->idAttribute}, $with);
    }

    /**
     * Deletes the file associated with the owner.
     * @throws CException if the file cannot be deleted or if the file id cannot be removed from the owner model.
     */
    public function deleteFile()
    {
        if (!$this->getManager()->deleteModel($this->owner->{$this->idAttribute})) {
            throw new CException('Failed to delete file.');
        }
        $this->owner->{$this->idAttribute} = null;
        if (!$this->owner->save(false)) {
            throw new CException('Failed to remove file id from owner.');
        }
    }

    /**
     * Returns the full path for the given model.
     * @return string the path.
     * @see FileManager::resolveFileUrl
     */
    public function resolveFileUrl()
    {
        return $this->loadFile()->resolveUrl();
    }

    /**
     * Returns the full url for the given model.
     * @return string the url.
     * @see FileManager::resolveFilePath
     */
    public function resolveFilePath()
    {
        return $this->loadFile()->resolvePath();
    }

    /**
     * Returns the file manager application component.
     * @return FileManager the component.
     */
    protected function getManager()
    {
        return Yii::app()->getComponent($this->managerID);
    }
}
