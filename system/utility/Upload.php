<?php
class Upload
{
	private $_filename;
	private $_destination;
	private $_max_filesize;
	private $_max_width;
	private $_max_height;
	private $_allowed_types;
	private $_error;

	/**
	 * Set destination for uploaded file
	 * 
	 * @param string $dest The path to move the uploaded file to
	 */
	function setDestination($dest)
	{
		$this->_destination = $dest;
	}

	/**
	 * Set max allowed file size in KB
	 * 
	 * @param int $filesize Size of file in KB
	 */
	function setMaxFilesize($filesize)
	{
		$this->_max_filesize = $filesize;
	}

	/**
	 * Set max width of image
	 * 
	 * @param int $width In pixel
	 */
	function setMaxWidth($width)
	{
		$this->_max_width = $width;
	}

	/**
	 * Set max height of image
	 * 
	 * @param int $height In pixel
	 */
	function setMaxHeight($height)
	{
		$this->_max_height = $height;
	}

	/**
	 * Upload file
	 * 
	 * @return boolean True on success
	 */
	function upload()
	{
		
	}

	/**
	 * Upload file and map it to table row
	 * 
	 * @return boolean True on success
	 */
	function image2Table($rel_object_manager, $rel_opject_id, $name)
	{
		$ext = File::getExt($_FILES['image']['name']);
	}

	function getError()
	{
		return $this->_error;
	}

	private function setError($msg)
	{
		$this->_error = $msg;
	}
}