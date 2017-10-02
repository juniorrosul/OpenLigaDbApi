<?php

namespace juniorrosul\OpenLigaDbApi;

use Carbon\Carbon;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfGoals;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfGroups;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfLeagues;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfMatches;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfMatchResults;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfSports;
use juniorrosul\OpenLigaDbApi\Api\ArrayOfTeams;
use juniorrosul\OpenLigaDbApi\Api\Goal;
use juniorrosul\OpenLigaDbApi\Api\Group;
use juniorrosul\OpenLigaDbApi\Api\League;
use juniorrosul\OpenLigaDbApi\Api\Location;
use juniorrosul\OpenLigaDbApi\Api\Match;
use juniorrosul\OpenLigaDbApi\Api\MatchResult;
use juniorrosul\OpenLigaDbApi\Api\Sport;
use juniorrosul\OpenLigaDbApi\Api\Team;
use juniorrosul\OpenLigaDbApi\Exception\EmptyEntityException;
use juniorrosul\OpenLigaDbApi\Exception\InvalidEntityException;
use juniorrosul\OpenLigaDbApi\Exception\InvalidResponseException;
use juniorrosul\OpenLigaDbApi\Model\Checkable;



class Client
{
    const WSDL_URL = 'http://www.OpenLigaDB.de/Webservices/Sportsdata.asmx?WSDL';

    protected $soapClient;

    protected $defaults = [
        'connection_timeout' => 5,
        'encoding' => 'UTF-8',
        'exceptions' => true,
        'soap_version' => SOAP_1_2,
        'classmap' => [
            'ArrayOfGoal' => ArrayOfGoals::class,
            'ArrayOfGroup' => ArrayOfGroups::class,
            'ArrayOfLeague' => ArrayOfLeagues::class,
            'ArrayOfMatchdata' => ArrayOfMatches::class,
            'ArrayOfMatchResult' => ArrayOfMatchResults::class,
            'ArrayOfSport' => ArrayOfSports::class,
            'ArrayOfTeam' => ArrayOfTeams::class,
            'Goal' => Goal::class,
            'Group' => Group::class,
            'League' => League::class,
            'Location' => Location::class,
            'Matchdata' => Match::class,
            'matchResult' => MatchResult::class,
            'Sport' => Sport::class,
            'Team' => Team::class,
        ]
    ];

    /**
     * @param string|null $wsdl URL of the api specifications
     * @param array $options additional SoapClient-options
     *
     * @throws \Exception if something went wrong :)
     * @return Client
     */
    public function __construct($wsdl=null, $options=[])
    {
        if($wsdl === null) {
            $wsdl = self::WSDL_URL;
        }

        if(!is_array($options)) {
            $options = [];
        }

        $options = array_merge($this->defaults, $options);

        try {
            $this->soapClient = new \SoapClient(
                $wsdl,
                $options
            );
        } catch(\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Get all available Leagues
     *
     * @return League[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getAvailableLeagues()
    {
        /** @var ArrayOfLeagues $leagues */
        $leagues = $this->doCall('GetAvailLeagues');

        return $leagues->getLeagues();
    }

    /**
     * Get all available Sports
     *
     * @return Sport[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getAvailableSports()
    {
        /** @var ArrayOfSports $sports */
        $sports = $this->doCall('GetAvailSports');

        return $sports->getSports();
    }

    /**
     * Get the available Groups for a given league and season
     * (e.g. Spieltag 1 (1) in 1. Bundesliga (bl1))
     *
     * @param string $league
     * @param integer $season
     *
     * @return Group[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getAvailableGroups($league, $season)
    {
        /** @var ArrayOfGroups $groups */
        $groups = $this->doCall('GetAvailGroups', [
            'leagueShortcut' => $league,
            'leagueSaison' => $season
        ]);

        return $groups->getGroups();
    }

    /**
     * Get all available Leagues by a given Sport ID
     * Get the Sport ID from getAvailableSports()
     *
     * @param integer $sportId
     *
     * @return League[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getAvailableLeaguesBySport($sportId)
    {
        /** @var ArrayOfLeagues $leagues */
        $leagues = $this->doCall('GetAvailLeaguesBySports', [
            'sportID' => $sportId,
        ]);

        return $leagues->getLeagues();
    }

    /**
     * Get the goals by a given Match ID
     * Get the Match ID from the getMatches...-methods
     *
     * @param integer $matchId
     *
     * @return Goal[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getGoalsByMatch($matchId)
    {
        /** @var ArrayOfGoals $goals */
        $goals = $this->doCall('GetGoalsByMatch', [
            'MatchID' => $matchId,
        ]);

        return $goals->getGoals();
    }

    /**
     * Get all goals for a given league and season
     * Make take some time :)
     *
     * @param string $league
     * @param integer $season
     *
     * @return Goal[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getGoalsByLeagueSeason($league, $season)
    {
        /** @var ArrayOfGoals $goals */
        $goals = $this->doCall('GetGoalsByLeagueSaison', [
            'leagueShortcut' => $league,
            'leagueSaison' => $season
        ]);

        return $goals->getGoals();
    }

    /**
     * Get the current group (aka Spieltag) for a given league
     *
     * @param string $league
     *
     * @return Group
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getCurrentGroup($league)
    {
        return $this->doCall('GetCurrentGroup', [
            'leagueShortcut' => $league,
        ]);
    }

    /**
     * Get the all matches by given group, league and season
     *
     * @param integer $groupOrderId
     * @param string $league
     * @param integer $season
     *
     * @return Match[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getMatchesByGroupLeagueSeason($groupOrderId, $league, $season)
    {
        /** @var ArrayOfMatches $matches */
        $matches = $this->doCall('GetMatchdataByGroupLeagueSaison', [
            'groupOrderID' => $groupOrderId,
            'leagueShortcut' => $league,
            'leagueSaison' => $season
        ]);

        return $matches->getMatches();
    }

    /**
     * Get all matches by given league and season
     *
     * @param string $league
     * @param integer $season
     *
     * @return Match[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getMatchesByLeagueSeason($league, $season)
    {
        /** @var ArrayOfMatches $matches */
        $matches = $this->doCall('GetMatchdataByLeagueSaison', [
            'leagueShortcut' => $league,
            'leagueSaison' => $season
        ]);

        return $matches->getMatches();
    }

    /**
     * Get all upcoming matches by a given league
     *
     * @param string $league
     * @param integer $season
     *
     * @return Match[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getUpcomingMatchesByLeague($league)
    {
        $matches = $this->doCall('GetMatchdataByLeagueSaison', [
            'leagueShortcut' => $league,
            'leagueSaison' => Carbon::now()->format('Y'),
        ]);

        $matches_filtered = array_filter($matches->getMatches(), function ($val, $key) {
            return !$val->isFinished();
        }, ARRAY_FILTER_USE_BOTH);

        return array_values($matches_filtered);

    }

    /**
     * Get all Teams given by League and Season
     *
     * @param $league
     * @param $season
     *
     * @return Team[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getTeamsByLeagueSeason($league, $season)
    {
        /** @var ArrayOfTeams $teams */
        $teams = $this->doCall('GetTeamsByLeagueSaison', [
            'leagueShortcut' => $league,
            'leagueSaison' => $season
        ]);

        return $teams->getTeams();
    }

    /**
     * Get last match from given League
     *
     * @param  string $league
     *
     * @return Match
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getLastMatch($league)
    {
        $match = $this->doCall('GetLastMatch', [
            'leagueShortcut' => $league
        ]);

        return $match;
    }

    /**
     * Get all Match between two Teams
     *
     * @param $teamId1
     * @param $teamId2
     *
     * @return Api\Match[]
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    public function getMatchesByTeams($teamId1, $teamId2)
    {
        /** @var ArrayOfMatches $matches */
        $matches = $this->doCall('GetMatchdataByTeams', [
            'teamID1' => $teamId1,
            'teamID2' => $teamId2,
        ]);

        return $matches->getMatches();
    }

    /**
     * Do the SOAP-Request
     *
     * @param $method
     * @param array $arguments
     *
     * @return Checkable
     *
     * @throws \SoapFault
     * @throws InvalidResponseException
     * @throws InvalidEntityException
     */
    protected function doCall($method, $arguments=[])
    {
        try {
            $resultObjectName = $method.'Result';
            $result = $this->soapClient->$method($arguments);

            if(!property_exists($result, $resultObjectName)) {
                throw new InvalidResponseException(sprintf(
                    'Object %s does not exist in SOAP Response',
                    $resultObjectName
                ), $result);
            }

            /** @var Checkable $result */
            $result = $result->$resultObjectName;

            if($result->isEmpty()) {
                throw new EmptyEntityException($result);
            }

            if(!$result->isValid()) {
                throw new InvalidEntityException($result);
            }

            return $result;
        } catch(\SoapFault $e) {
            throw $e;
        }
    }
}
