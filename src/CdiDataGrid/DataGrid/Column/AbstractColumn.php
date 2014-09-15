<?php

namespace CdiDataGrid\DataGrid\Column;

/**
 * Description of AbstractColumn
 *
 * @author cincarnato
 */
abstract class AbstractColumn {

    protected $name;
    protected $visualName;
    protected $hidden = false;
    protected $tooltip;
    protected $htmlBegin;
    protected $htmlEnd;
    protected $type = "text";
    protected $replaceTrueBy;
    protected $replaceFalseBy;
    protected $formatDatetime;

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

}

?>
