<?php

/*
 * This index.php is actual useless, it only redirects user to /public
 * /public would usually be deployed in the server root to prevent remote users from
 * accessing files in /application directly
 */

header('Location: ./public/');
