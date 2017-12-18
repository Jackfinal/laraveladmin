<?php
namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ChinaArea;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        $Filename = date('Ymd');
        
        Excel::create($Filename, function($excel) {
            
            $excel->sheet('Sheetname', function($sheet) {
                $sheet->row(1, ['举报录入']);
                $sheet->mergeCells('A1:AB1','center');
                $sheet->row(2, [
                    '举报人姓名','性别','电子邮箱','电话号码','省','市','县',
                    '通讯地址','危害类型大类','危害类型小类','危害方式','被举报类型',
                    '搜索引擎类型','(其他)具体搜索引擎名','网站（APP）名称','举报来源','举报关键字','详细网址',
                    '举报信息内容','下载源类别','其他下载源','APP所在栏目','通讯工具名称','(其他)具体通讯工具名称','网盘名称',
                    '(其他)具体网盘名称','账号','账号性质'
                ]);
                //$sheet->setAutoFilter('B1:');
                //echo '<pre>';
                
                // 这段逻辑是从表格数据中取出需要导出的字段
                $rows = collect($this->getData())->map(function ($item) {
                    $temp =$this->doData($item);
                    return $temp;
                });

                $sheet->rows($rows);

            });

        })->export('xls');
    }
    
    protected function getArea($id)
    {
        if(!$id)return '';
        $city = ChinaArea::where('id', $id)->first();
        if(!$city)return '';
        return $city['name'];
    }
    protected function doData($item)
    {
        $temp = [
            'real_name' => $item['real_name'],
            'real_sex' => $item['real_sex'],
            'real_email' => $item['real_email'],
            'real_mobile' => $item['real_mobile'],
            'real_provinces' => '安徽',
            'real_city' => $this->getArea($item['real_city']),
            'real_district' => $this->getArea($item['real_district']),
            'real_address' => $item['real_address'],
            'harm_type' => $item['harm_type'],
            'harm_type1' => '',
            'harm_type2' => '',
            'report_type' => $item['report_type'],
            'seids' => $item['seids'],
            'otherseids' => $item['otherseids'],
            'report_webname' => $item['report_webname'],
            'source'  => '',
            'keyword' => $item['keyword'],
            'report_detail_url' => $item['report_detail_url'],
            'report_content' => $item['report_content'],
            'report_other_appsource' => $item['report_other_appsource'],
            'report_other_appsource1' => '',
            'report_columm' => $item['report_columm'],
            'toolname' => $item['toolname'],
            'toolothername' => $item['toolothername'],
            'drivename' => $item['drivename'],
            'driveothername' => $item['driveothername'],
            'accountname' => $item['accountname'],
            'accountnature' => $item['accountnature'],
        
        ];
        return $temp;
    }
}