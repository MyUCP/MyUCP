<?php

class RouteMatch
{
    /**
     * Parse URI to regex pattern
     *
     * @param Route $route
     *
     * @return string
     */
    public static function uriToRegex(Route $route)
    {
        if($route->uri{0} == "/" && !empty($route->uri{1}))
            $route->uri = substr($route->uri, 1);

        $regexUri = '/^' . preg_replace('/\//', '\/', $route->uri) .  '\/?$/';

        if(preg_match_all('/\{([a-z]+):(.*?)\}/', $route->uri, $preg)){
            for($i = 0; $i < count($preg[0]); $i++) {
                $regexUri = str_replace($preg[0][$i], '(' . $preg[2][$i] . ')', $regexUri);
            }
        }

        return $regexUri;
    }

    /**
     * Parse URI parameter names to array
     *
     * @param Route $route
     *
     * @return array
     */
    public static function parseParameterNames(Route $route)
    {
        $params = [];

        preg_match_all('/\{([a-z]+):(.*?)\}/', $route->uri, $preg);

        for($i = 0; $i < count($preg[0]); $i++){
            $params[] = $preg[1][$i];
        }

        return $params;
    }

    /**
     * Parse URI parameter patterns to array
     *
     * @param Route $route
     *
     * @return array
     */
    public static function parseParameterPatterns(Route $route)
    {
        $params = [];

        preg_match_all('/\{([a-z]+):(.*?)\}/', $route->uri, $preg);

        for($i = 0; $i < count($preg[0]); $i++){
            $params[] = $preg[2][$i];
        }

        return $params;
    }

    /**
     * Check the pattern to reflect the current address
     *
     * @param Route $route
     * @param Request $request
     *
     * @return bool
     */
    public static function parseUri(Route $route, Request $request)
    {
        if(preg_match($route->regexUri, $request->path(), $preg)) {

            $route->addParameters(static::parseParameters($route, $preg));

            return true;
        }

        return false;
    }

    /**
     * Retrieves all settings from the current address according to the pattern
     *
     * @param Route $route
     * @param array $preg
     *
     * @return array
     */
    public static function parseParameters(Route $route, array $preg)
    {
        $parameters = [];

        for($i = 1; $i < count($preg); $i++){
            $parameters[$route->parameterNames[$i - 1]] = $preg[$i];
        }

        return $parameters;
    }

    /**
     * Validate Route domain
     *
     * @param Route $route
     * @param Request $request
     * @return bool
     */
    public static function validateDomain(Route $route, Request $request)
    {
        if(!isset($route->action['domain']))
            return true;

        return self::parseDomain($route, $request);
    }

    /**
     * Check the pattern to reflect the current domain
     *
     * @param Route $route
     * @param Request $request
     * @return bool
     */
    public static function parseDomain(Route $route, Request $request)
    {
        $domain = $route->action['domain'];

        if($domain == $request->domain()) {
            return true;
        }

        $regex = '/' . preg_replace('/\//', '\/', $domain) .  '/';
        $params = [];

        if(preg_match_all('/\{([a-z]+):(.*?)\}/', $domain, $preg)) {
            for($i = 0; $i < count($preg[0]); $i++){
                $params[] = $preg[1][$i];
                $regex = str_replace($preg[0][$i], '(' . $preg[2][$i] . ')', $regex);
            }

            if(!(preg_match($regex, $request->domain(), $preg))) {
                return false;
            }

            $parameters = [];

            for ($i = 0; $i < count($params); $i++) {
                $parameters[$params[$i]] = $preg[$i + 1];
            }

            $route->addParameters($parameters);

            return true;
        }

        return false;
    }

    /**
     * Verify CSRF token to route, if verification needs
     *
     * @param Route $route
     * @param Request $request
     *
     * @return bool
     */
    public static function csrfVerify(Route $route, CsrfToken $csrfToken)
    {
        if(!$route->csrf_verify)
            return true;

        return $csrfToken->check();
    }
}