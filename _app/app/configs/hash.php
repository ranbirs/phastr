<?php

namespace app\configs;

class Hash
{

	const key__ = 'secret key'; /* Default hashing key */

	const algo__ = 'sha512';

	const cipher__ = '$2a$';

	const cost__ = 10;

	const salt__ = 22;

	const chars__ = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

}