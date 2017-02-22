<?php

set_include_path(dirname(__DIR__));

spl_autoload_register();

set_error_handler('\\sys\\handlers\\Error::error');
set_exception_handler('\\sys\\handlers\\Error::exception');