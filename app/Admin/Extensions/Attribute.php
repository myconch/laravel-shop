<?php
namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Attribute
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
        
        $('.attribute').on('click',function () {
        // nothing
SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success attribute' data-id='{$this->id}' href='/admin/products/{$this->id}/edit'>规格</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
