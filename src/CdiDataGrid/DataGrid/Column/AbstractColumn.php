<?php

namespace CdiDataGrid\DataGrid\Column;

/**
 * Description of AbstractColumn
 *
 * @author cincarnato
 */
abstract class AbstractColumn implements InterfaceColumn {

    protected $name;
    protected $visualName;
    protected $hidden = false;
    protected $tooltip;
    protected $htmlBegin;
    protected $htmlEnd;
    protected $type = "text";
    protected $replaceTrueBy;
    protected $filePath;
    protected $fileWidth = "100%";
    protected $fileHeight= "100%";
    protected $replaceFalseBy;
    protected $formatDatetime;
    protected $length = 15;
     protected $helper;
     protected $customData = array();
 

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    function __construct($name) {
        $this->name = $name;
    }

    public function __toString() {
        return $this->visualName;
    }

    public function getVisualName() {
        return $this->visualName;
    }

    public function setVisualName($visualName) {
        $this->visualName = $visualName;
    }

    public function getHidden() {
        return $this->hidden;
    }

    public function setHidden($hidden) {
        $this->hidden = $hidden;
    }

    public function getTooltip() {
        return $this->tooltip;
    }

    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;
    }

    public function getHtmlBegin() {
        return $this->htmlBegin;
    }

    public function setHtmlBegin($htmlBegin) {
        $this->htmlBegin = $htmlBegin;
    }

    public function getHtmlEnd() {
        return $this->htmlEnd;
    }

    public function setHtmlEnd($htmlEnd) {
        $this->htmlEnd = $htmlEnd;
    }

    public function getReplaceTrueBy() {
        return $this->replaceTrueBy;
    }

    public function setReplaceTrueBy($replaceTrueBy) {
        $this->replaceTrueBy = $replaceTrueBy;
    }

    public function getReplaceFalseBy() {
        return $this->replaceFalseBy;
    }

    public function setReplaceFalseBy($replaceFalseBy) {
        $this->replaceFalseBy = $replaceFalseBy;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getFormatDatetime() {
        return $this->formatDatetime;
    }

    public function setFormatDatetime($formatDatetime) {
        $this->formatDatetime = $formatDatetime;
    }

    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    function getFilePath() {
        return $this->filePath;
    }

    function setFilePath($filePath) {
        $this->filePath = $filePath;
    }
    function getFileWidth() {
        return $this->fileWidth;
    }

    function getFileHeight() {
        return $this->fileHeight;
    }

    function setFileWidth($fileWidth) {
        $this->fileWidth = $fileWidth;
    }

    function setFileHeight($fileHeight) {
        $this->fileHeight = $fileHeight;
    }
    
    function getHelper() {
        return $this->helper;
    }

    function setHelper($helper) {
        $this->helper = $helper;
    }

    function getCustomData() {
        return $this->customData;
    }

    function setCustomData($customData) {
        $this->customData = $customData;
    }





}

?>
