<?php
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class PlgSystemMyLoginButton extends CMSPlugin
{
    protected $app;

    public function onAfterRender()
    {
        // Kiểm tra xem có phải là trang đăng nhập hay không
        $option = $this->app->input->getCmd('option');
        $view = $this->app->input->getCmd('view');

        if ($option === 'com_users' && $view === 'login') {
            // Lấy nội dung body hiện tại
            $body = $this->app->getBody();

            // HTML của nút
            $buttonHtml = '<div class="custom-login-button"><button id="my-login-button">Click Me</button></div>';

            // Thêm HTML của nút vào trước thẻ đóng </form>
            $body = str_replace('</form>', $buttonHtml . '</form>', $body);

            // Cập nhật lại body với nội dung đã chèn
            $this->app->setBody($body);
        }
    }
}

