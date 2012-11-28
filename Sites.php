<?php

class EtuDev_Util_Sites {

	static public function getTwitterProfileURL($twitter_username) {
		return str_ireplace("@", "http://twitter.com/#!/", $twitter_username);
	}

}
