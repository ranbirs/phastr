<?php
header('Content-Type: ' . $this->type);
print base64_decode($this->body);
