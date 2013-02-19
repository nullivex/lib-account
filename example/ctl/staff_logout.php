<?php

StaffSession::tokenDestroy(StaffSession::getTokenFromSession());
StaffSession::destroySession();
redirect(Url::login());