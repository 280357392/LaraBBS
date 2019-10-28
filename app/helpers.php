<?php

/**
 * 此方法会将当前请求的路由名称转换为 CSS 类名称，
 * 作用是允许我们针对某个页面做页面样式定制。在后面的章节中会用到。
 *
 * @return mixed
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}


/**
 * {{ category_nav_active(1) }}
 * active_class 的用法
 * 如果 $condition 不为 False 即会返回字符串 `active`
 */
function category_nav_active($category_id)
{
    return active_class(
        (
            //当前访问的URL是：http://larabbs.test/categories/{category_id}  这个方法就返回 "active"
            //if_route () - 判断当前对应的路由是否是指定的路由；
            //if_route_param () - 判断当前的 url 有无指定的路由参数。
            if_route('categories.show') && if_route_param('category', $category_id)
        )
    );
}


function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return Str::limit($excerpt, $length);
}
