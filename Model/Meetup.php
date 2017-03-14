<?php

namespace Sivaschenko\Cli\Model;

use Symfony\Component\Console\Helper\ProgressBar;

class Meetup
{
    /**#@+
     * Meetup.com API urls
     */
    const API_URL_RSVPS = 'https://api.meetup.com/%s/events/%s/rsvps';
    const API_URL_MEMBERS = 'https://api.meetup.com/members/%s';
    /**#@-*/

    /**
     * @param string $meetupGroupUrl
     * @param int|string $meetupId
     * @param ProgressBar $progressBar
     * @return array
     */
    public function getRsvps($meetupGroupUrl, $meetupId, ProgressBar $progressBar)
    {
        $names = [];
        $info = $this->getRsvpsInfo($meetupGroupUrl, $meetupId);
        $progressBar->start(count($info));
        foreach ($info as $rsvp) {
            if (isset($rsvp['member']['name'])) {
                $names[] = [$rsvp['member']['name'], $this->getJoinDate($rsvp, $progressBar)];
            }
        }
        $progressBar->finish();
        return $names;
    }

    /**
     * @param array $rsvp
     * @param ProgressBar $progressBar
     * @return false|string
     */
    private function getJoinDate(array $rsvp, ProgressBar $progressBar)
    {
        $date = 'undefinded';
        if (isset($rsvp['member']['id'])) {
            $member = $this->getMember($rsvp['member']['id']);
            if ($member && isset($member['joined'])) {
                $date = date('jS F, Y', $member['joined']/1000);
            }
        }
        $progressBar->advance();
        return $date;
    }

    /**
     * @return array
     */
    private function getRsvpsInfo($meetupGroupUrl, $meetupId)
    {
        return json_decode(file_get_contents(sprintf(static::API_URL_RSVPS, $meetupGroupUrl, $meetupId)), true);
    }

    /**
     * @param int|string $id
     * @return array
     */
    private function getMember($id)
    {
        return json_decode(file_get_contents(sprintf(static::API_URL_MEMBERS, $id)), true);
    }
}
