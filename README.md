# MobileEsp for ExpressionEngine <sup>[1](#user-content-notes-1), [2](#user-content-notes-2)</sup>

So far all the mobile add-ons basically redirect you to another version of your site.

What responsive site needs that?

What if you just want a quick check to provide a difference for user experience, site performance, etc.?

This extension provides arbitrary, early-parsed global conditionals (think [Mo Variables](https://github.com/rsanchez/mo_variables) <sup>[3](#user-content-notes-3)</sup>) using the [The MobileEsp Project](http://www.hand-interactive.com/detect/mobileesp_demo_php.htm).


## Installation

If you haven't changed where your add-ons are installed, copy the `mdetect` directory into your `system/expressionengine/third_party` directory.

## Conditionals

    {if is_android}
    {if is_ios}
    {if is_ipad}
    {if is_iphone}
    {if is_iphone_or_ipod}
    {if is_android_iphone_or_ipod}
    {if is_mobile}
    {if is_tablet}

    {if is_not_android}
    {if is_not_ios}
    {if is_not_ipad}
    {if is_not_iphone}
    {if is_not_iphone_or_ipod}
    {if is_not_android_iphone_or_ipod}
    {if is_not_mobile}
    {if is_not_tablet}

    {if is_ios_or_android}
    {if neither_mobile_nor_ipad}
    {if neither_mobile_android_nor_ios}

### Notes

1. <a name="user-content-notes-1"></a> Initially developed in EE 2.5, compatible with EE 2.8 
2. <a name="user-content-notes-2"></a> Though the project is named EE-MobileEsp, the add-on itself goes by `mdetect`
3. <a name="user-content-notes-3"></a> Mo Variables recently added mobile detection, while I haven't encountered any conflicts you may want to leave its mobile detection off in its Extension Settings.
