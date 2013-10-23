<?php
/**
 * FileResource class file.
 * @author Christoffer Lindqvist <christoffer.lindqvist@nordsoftware.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.resources
 */

/**
 * Component that represents a file resource.
 */
abstract class FileResource extends CComponent
{
    /**
     * Returns the file extension.
     * @return string the extension.
     */
    abstract public function getExtension();

    /**
     * Returns the file name.
     * @return string the filename.
     */
    abstract public function getName();

    /**
     * Returns the file mime type.
     * @return string the file mime type.
     */
    abstract public function getMimeType();

    /**
     * Returns the files size in bytes.
     * @return int the file size.
     */
    abstract public function getSize();

    /**
     * Saves the file to the specified path.
     * @param string $path the path where to save the file.
     * @return bool if the file was successfully saved.
     */
    abstract public function saveAs($path);
}