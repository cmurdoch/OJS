<?php

/**
 * @defgroup api_v1_invitations Invitation API requests
 */

/**
 * @file api/v1/invitations/index.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup api_v1_users
 *
 * @brief Handle requests for invitations API functions.
 *
 */
return new \PKP\handler\APIHandler(new \PKP\API\v1\invitations\PKPInvitationController());
