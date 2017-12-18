<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class ExpandRow extends AbstractDisplayer
{
    public function display(\Closure $callback = null, $btn = '')
    {
        $callback = $callback->bindTo($this->row);

        $html = call_user_func($callback);

        $script = <<<EOT

$('.grid-expand').on('click', function () {
    if ($(this).data('inserted') == '0') {
        var key = $(this).data('key');
        var row = $(this).closest('tr');
        var html = $('template.grid-expand-'+key).html();

        row.after("<tr><td colspan='"+row.find('td').length+"' style='padding:0 !important; border:0px;'>"+html+"</td></tr>");

        $(this).data('inserted', 1);
    }

    $("i", this).toggleClass("fa-caret-right fa-caret-down");
    replayButton();
    $('.Smeta').parent().attr('colspan', 2);
});
EOT;
        Admin::script($script);

        $btn = $btn ?: $this->column->getName();

        $key = $this->getKey();
        
        switch (isset($this->row->status)?$this->row->status:0)
        {
            case '1':
                $class = 'btn-info';
                break;
            case '2':
                $class = 'btn-success';
                break;
            case '3':
                $class = 'btn-danger';
                break;
            default:
                $class = 'btn-default';
                break;
        } 

        return <<<EOT
<a class="btn btn-xs {$class} grid-expand" data-inserted="0" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
    <i class="fa fa-caret-right"></i> $btn
</a>
<template class="grid-expand-{$key}">
    <div id="grid-collapse-{$key}" class="collapse">$html</div>
</template>
EOT;
    }
}