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
     * Saves the given file both in the database and on the hard disk.
     * @param string $name new name for the file.
     * @param string $path path relative to the base path.
     * @param array $saveAttributes attributes that should be passed to the save method.
     * @return File the model.
     * @see FileManager::saveModel
     */
    public function saveFile($name = null, $path = null, $saveAttributes = array())
    {
        $this->owner->{$this->uploadAttribute} = CUploadedFile::getInstance(
            $this->owner,
            $this->uploadAttribute
        );
        $model = $this->getFileManager()->saveModel($this->owner->{$this->uploadAttribute}, $name, $path);
        foreach (array($this->idAttribute, $this->uploadAttribute) as $attribute) {
            if (!in_array($attribute, $saveAttributes)) {
                $saveAttributes[] = $attribute;
            }
        }
        $this->owner->{$this->idAttribute} = $model->id;
        if (!$this->owner->save(true, $saveAttributes)) {
            throw new CException('Could not save active record.');
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
        $id = $this->owner->{$this->idAttribute};
        return $this->getFileManager()->loadModel($id);
    }

    /**
     * Deletes the file associated with the owner.
     * @return boolean whether the file was deleted.
     */
    public function deleteFile()
    {
        $id = $this->owner->{$this->idAttribute};
        return $this->getFileManager()->deleteModel($id);
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
        return $model->resolveUrl($model);
    }

    /**
     * Returns the full url for the given model.
     * @param File $model the file model.
     * @return string the url.
     * @see FileManager::resolveFilePath
     */
    public function resolveFilePath()
    {
        $model = $this->loadFile();
        return $model->resolvePath($model);
    }

    /**
     * Returns the file manager application component.
     * @return FileManager the component.
     */
    protected function getFileManager()
    {
        return Yii::app()->getComponent($this->componentID);
    }
}
