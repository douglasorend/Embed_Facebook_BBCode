--------

# EMBED FACEBOOK BBCODE v3.10
[**By Dougiefresh**](http://www.simplemachines.org/community/index.php?action=profile;u=253913) -> [Link to Mod](http://custom.simplemachines.org/mods/index.php?mod=4062)

--------

## Introduction
This modification adds a BBCode to embed Facebook posts and videos into your forum.  Embedding posts and videos are simple way to put public posts and videos - by a Page or a person on Facebook - into the content of your web site or web page.  Only public posts from Facebook Pages and profiles can be embedded.

The BBCode takes these forms:
    
    [facebook]{facebook URL}[/facebook]
    [facebook width={x}]{facebook URL}[/facebook]
    
where **{facebook URL}** is the URL to the facebook photo post, post or video that you want to display and **{x}** is the width of the post desired.  Note that if the width isn't specified, the global setting will be applied.

## User Profile Settings
Under **Profile** => **Look and Layout**, there is a new option called **Language the Facebook controls are shown in**.  It's purpose is set the language of the Facebook controls used in each post that the user can see, which is overridden by Facebook when the user is already logged in.  Please note that setting this option only controls the default language of the FB controls, not the posts themselves, and does **NOT** translate the posts into a different language!

## Admin Settings
Under **Admin** => **Modification Settings**, there are some new options:

- **Language the Facebook controls are shown in**.  It's purpose is to define the forum's default language for the Facebook post, which is overridden by Facebook when the user is already logged in.  Please note that setting this option only controls the default language of the FB controls, not the posts themselves, and does **NOT** translate the posts into a different language!
- **Default width of embedded Facebook video**
- **Default height of embedded Facebook video**
- **Include Facebook link beneath?**

## Further Information
- [Facebook Embedded Posts](https://developers.facebook.com/docs/plugins/embedded-posts), specifically **Getting a posts's URL**

## Compatibility Notes
This mod was tested on SMF 2.0.10, but should work on SMF 2.1 Beta 2, as well as SMF 2.0 and up.  SMF 1.x is not and will not be supported.

## Changelog
The changelog can be viewed at [XPtsp.com](http://www.xptsp.com/board/free-modifications/embed-facebook-bbcode/).

## License
Copyright (c) 2015 - 2018, Douglas Orend
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.