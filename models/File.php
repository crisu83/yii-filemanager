<?php
/**
 * File class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.models
 */

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property string $name
 * @property string $extension
 * @property string $path
 * @property string $filename
 * @property string $mimeType
 * @property integer $byteSize
 * @property string $hash
 * @property string $createdAt
 */
class File extends CActiveRecord
{
    /** @var FileManager */
    protected $_manager;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return File the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, extension, filename, mimeType, byteSize, createdAt', 'required'),
            array('name, path, extension, filename, mimeType, hash', 'length', 'max' => 255),
            array('byteSize', 'length', 'max' => 10),
            // The following rule is used by search().
            array('id, name, path, extension, filename, mimeType, byteSize, hash, createdAt', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'        => Yii::t('label', 'ID'),
            'name'      => Yii::t('label', 'Name'),
            'path'      => Yii::t('label', 'Path'),
            'extension' => Yii::t('label', 'Extension'),
            'filename'  => Yii::t('label', 'Filename'),
            'mimeType'  => Yii::t('label', 'Mime type'),
            'byteSize'  => Yii::t('label', 'Byte size'),
            'hash'      => Yii::t('label', 'Hash'),
            'createdAt' => Yii::t('label', 'Created'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('extension', $this->extension, true);
        $criteria->compare('filename', $this->filename, true);
        $criteria->compare('mimeType', $this->mimeType, true);
        $criteria->compare('byteSize', $this->byteSize, true);
        $criteria->compare('hash', $this->hash, true);
        $criteria->compare('createdAt', $this->createdAt, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the full filename for this file.
     * @param string $extension the file extension.
     * @return string the filename.
     */
    public function resolveFilename($extension = null)
    {
        if ($extension === null) {
            $extension = $this->extension;
        }
        return $this->name . '-' . $this->id . '.' . $extension;
    }

    /**
     * Returns the url for this file.
     * @param boolean $absolute whether the url should be absolute.
     * @return string the url.
     */
    public function resolveUrl($absolute = false)
    {
        return $this->_manager->getBaseUrl($absolute) . '/' . $this->resolveInternalPath();
    }

    /**
     * Returns the path for this file.
     * @param boolean $absolute whether the path should be absolute.
     * @return string the path.
     */
    public function resolvePath($absolute = true)
    {
        return $this->_manager->getBasePath($absolute) . '/' . $this->resolveInternalPath();
    }

    /**
     * Returns the internal path to the within the sub-directory.
     * @return string the path.
     */
    public function resolveInternalPath()
    {
        return $this->getPath() . $this->resolveFilename();
    }

    /**
     * Returns the contents of this file as a string.
     * @return string the contents.
     */
    public function getContents()
    {
        return readfile($this->resolvePath());
    }

    /**
     * Returns the hash for this file.
     * Override this method to change how the hash is calculated.
     * @return string the hash.
     */
    public function calculateHash()
    {
        return hash_file('md5', $this->resolvePath());
    }

    /**
     * Returns the internal path to this file.
     * @return string the path.
     */
    public function getPath()
    {
        return $this->path !== null ? $this->path . '/' : '';
    }

    /**
     * Returns the file manager component.
     * @param FileManager $manager the component.
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
    }
}
