<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Config::load();
        //共享菜单数据
        view()->share('menu', \App\Models\Index::menu());
        //自定义指令
        Blade::directive('Articles', function($where){
            $list = '';
            return '<?php unset($list); $list = \App\Models\Index::getList("'.$where.'");?>';
        });
        Blade::directive('defaultVar', function($string){
            $list = '';
            return '<?php echo isset('.$string.')?'.$string.':"";?>';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
