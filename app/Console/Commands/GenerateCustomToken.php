<?php

namespace App\Console\Commands;

use App\Handlers\CustomTokenHandler;
use App\Models\WechatUser;
use Illuminate\Console\Command;

class GenerateCustomToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom_token {wechatUserId=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wechatUserId = $this->argument('wechatUserId');

        if (WechatUser::where('id', $wechatUserId)->doesntExist()) {
            $this->error('用户不存在!');
        } else {
            $token = CustomTokenHandler::createSuper($wechatUserId);
            $this->info('超级custom_token 创建成功!');
            $this->info($token);
        }
    }
}
