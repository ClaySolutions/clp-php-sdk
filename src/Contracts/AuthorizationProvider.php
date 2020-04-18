<?php
declare(strict_types=1);
namespace Clay\CLP\Contracts;

interface AuthorizationProvider {

	public function generateAuthorizationHeader(): string;

}