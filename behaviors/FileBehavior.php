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
     * @var string name of the model attribute that holds the file id (defaults to 'fileId').
     */
    public $idAttribute = 'fileId';

    /**
     * @var string name of the model attribute that holds the uploaded file (defaults to 'upload').
     */
    public $uploadAttribute = 'upload';

    /**
     * @var string the component id for the file manager component (defaults to 'fileManager').
     */
    public $managerID = 'fileManager';

    /**
     * Saves the given file both in the database and on the hard disk.
     * @param string $name new name for the file.
     * @param string $path path relative to the base path.
     * @param array $saveAttributes attributes that should be passed to the save method.
     * @param string $scenario name of the scenario.
     * @return File the model.
     * @throws CException if the owner cannot be saved.
     * @see FileManager::saveModel
     */
    public function saveFile($name = null, $path = null, $saveAttributes = array(), $scenario = 'insert')
    {
        $this->owner->{$this->uploadAttribute} = CUploadedFile::getInstance(
            $this->owner,
            $this->uploadAttribute
        );
        if (!in_array($this->uploadAttribute, $saveAttributes)) {
            $saveAttributes[] = $this->uploadAttribute;
        }
        if (!$this->owner->validate($saveAttributes)) {
            throw new CException('Failed to save file.');
        }
        $model = $this->getManager()->saveModel($this->owner->{$this->uploadAttribute}, $name, $path, $scenario);
        $this->owner->{$this->idAttribute} = $model->id;
        if (!$this->owner->save(true, array($this->idAttribute))) {
            throw new CException('Failed to save file id to owner.');
        }
        return $model;
    }

    /**
     * Returns the file with the given id.
     * @return File the model.
     * @see FileManager::loadModel
     */
    public function loadFile()
    {
        return $this->getManager()->loadModel($this->owner->{$this->idAttribute});
    }

    /**
     * Deletes the file associated with the owner.
     * @return boolean whether the file was deleted.
     */
    public function deleteFile()
    {
        return $this->getManager()->deleteModel($this->owner->{$this->idAttribute});
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
