MobileEsp Extension for ExpressionEngine 2.5.x
==============================================

So far all the mobile extensions that exist basically redirect you to another version of the site.

I haven't come across any extensions that provide device or feature detection so you can alter content.

This is just a quick port of [MobileEsp](http://www.hand-interactive.com/detect/mobileesp_demo_php.htm) for things that I use the original PHP version for.

This extension provides arbitary global conditionals from the above library.

Current conditionals:

    {if is_mobile}
    {if is_tablet}
    {if is_ios}
    {if is_iphone}
    {if is_ipad}
    {if is_android}
    {if is_not_android}

    {if is_ios_or_android}
    {if neither_mobile_nor_ipad}
    {if neither_mobile_android_nor_ios}
