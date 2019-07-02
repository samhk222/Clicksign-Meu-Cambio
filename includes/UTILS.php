<?php
use function GuzzleHttp\json_decode;

class UTILS
{
    public static function handleResponse($response)
    {
        if (is_array($response)) {
            if (self::isJson($response['body'])) {
                $response['body'] = print_r(json_decode($response['body']), true);
            }

            return <<<EOL
            <table width="90%" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Code: <h4><span class="label label-primary">{$response['code']}</span></h4></th>
                        <th>Reason: <h4><span class="label label-primary">{$response['reason']}</span></h4></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><pre>{$response['body']}</pre></td>
                    </tr>
                </tbody>
            </table>
EOL;
        }
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
