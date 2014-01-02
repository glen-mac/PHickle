PHickle
=======

A PHP Socket Interface Library for the game Club Penguin

```
// +--------------------------------------------------------------------------
// | PHickle v0.41
// | ========================================
// | by Glenn McGuire, 2013
// | https://github.com/glen-mac
// | ========================================
// +--------------------------------------------------------------------------
// | THIS LIBRARY IS FREE SOFTWARE
// | RELEASED UNDER THE: GNU GENERAL PUBLIC LICENSE (GPL) V2
// +--------------------------------------------------------------------------
// | > Feel free to edit, use and release this library
// | > Please do not use this library in commercial software
// | > Because proprietary software < Open Source
// +--------------------------------------------------------------------------
```

## Requirements ##

This library has no external dependencies; you only need an active internet connection.

## Basic Usage ##

Here is a basic script example:

```PHP
<?php

require '/Core/System.php'; //References a system file

$Test = new Penguin('USERNAME', 'PASSWORD', 'SERVER NAME'); //Enter your Username, Password and desired Server here

while ($Test->LoginStat = True)
{
	$Test->joinRoom(310); //Uses one of the functions to join a server (example)


	break;
}

$Test->disconnect(); //Disconnects from the socket

?>
```


### Other Basic Functions ###

```PHP
$Test->joinRoom($RoomdID, $X = 405, $Y = 360);
//Joins a room with the desired room ID and coordinates. If no coordinates are entered, it will default to (405, 360).

$Test->sendMessage($Message = "hello");
//Sends an in-game message, will default with 'hello' if no argument is entered.

$Test->sendPosition($X = 0, $Y = 0);
//Will move your in-game character to a location. Will default to room centre if no arguments are passed.

$Test->addItem($ID);
//Will add the desired item to your account.

$Test->addFurniture($ID);
//Will add the desired furniture item to your account.

$Test->throwSnowball($X = 0, $Y = 0);
//Will throw a snowball at the desired position. Will default to room centre if no arguments are passed.

$Test->sendEmote($ID);
//Will send an emote with the passed ID.

$Test->sendJoke($ID);
//Will send a joke with the passed ID.
```

## Support ##

For help with this library, its use and its development please contact me at glennmcguire9@gmail.com

## Copyright and License ##

This is free software, licensed under the GNU Lesser General Public License version 2.0 or later.

## Thanks ##

Thanks to ClubPenguin for implementing such a simple, and easy to emulate packet interaction system between client and server.











