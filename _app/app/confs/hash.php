<?php

namespace app\confs;

class Hash
{

	const key__ = 'session%hashing'; /* Key for session hashing */

	const algo__ = 'sha256';

	const cipher__ = '$2a$';

	const cost__ = 10;

	const salt__ = 22;

	const chars__ = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

}