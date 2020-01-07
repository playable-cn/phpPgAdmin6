<?php

/**
 * PHPPgAdmin v6.0.0-RC1.
 */

namespace PHPPgAdmin\Controller;

use PHPPgAdmin\Decorators\Decorator;

/**
 * Base controller class.
 */
class HistoryController extends BaseController
{
    use \PHPPgAdmin\Traits\ServersTrait;
    public $EOF;
    public $fields;
    public $scripts          = '<script type="text/javascript">window.inPopUp=true;</script>';
    public $controller_title = 'strhistory';

    /**
     * Default method to render the controller according to the action parameter.
     */
    public function render()
    {
        switch ($this->action) {
            case 'confdelhistory':
                $this->doDelHistory($_REQUEST['queryid'], true);

                break;
            case 'delhistory':
                if (isset($_POST['yes'])) {
                    $this->doDelHistory($_REQUEST['queryid'], false);
                }

                $this->doDefault();

                break;
            case 'confclearhistory':
                $this->doClearHistory(true);

                break;
            case 'clearhistory':
                if (isset($_POST['yes'])) {
                    $this->doClearHistory(false);
                }

                $this->doDefault();

                break;
            case 'download':
                return $this->doDownloadHistory();
            default:
                $this->doDefault();
        }

        // Set the name of the window
        $this->setWindowName('history');

        return $this->printFooter(true, 'footer_sqledit.twig', ['inPopUp' => true, 'windowname' => 'sqledit']);
    }

    private function _getSessionHistory($server, $database)
    {
        if (!array_key_exists('history', $_SESSION)) {
            return null;
        }
        $session_history = $_SESSION['history'];

        if (!array_key_exists($server, $session_history)) {
            return null;
        }
        $server_history = $session_history[$server];

        if (!array_key_exists($database, $server_history)) {
            return null;
        }
        return $server_history[$database];

    }

    public function doDefault()
    {
        $data = $this->misc->getDatabaseAccessor();

        $this->printHeader($this->headerTitle(), $this->scripts, true, 'header.twig');

        // Bring to the front always
        echo '<body onload="window.focus();">' . PHP_EOL;

        echo '<form action="' . \SUBFOLDER . '/src/views/history" method="post">' . PHP_EOL;
        $this->printConnection('history');
        echo '</form><br />';

        if (
            !$request_server = $this->getRequestParam('server', null) &&
            !$request_database = $this->getRequestParam('database', null)
        ) {
            echo "<p>{$this->lang['strnodatabaseselected']}</p>" . PHP_EOL;
            return;
        }
        $sessionHistory = $this->_getSessionHistory($request_server, $request_database);
        if (!is_null($sessionHistory)) {

            $history = new \PHPPgAdmin\ArrayRecordSet($sessionHistory);

            //Kint::dump($history);
            $columns = [
                'query'    => [
                    'title' => $this->lang['strsql'],
                    'field' => Decorator::field('query'),
                ],
                'paginate' => [
                    'title' => $this->lang['strpaginate'],
                    'field' => Decorator::field('paginate'),
                    'type'  => 'yesno',
                ],
                'actions'  => [
                    'title' => $this->lang['stractions'],
                ],
            ];

            $actions = [
                'run'    => [
                    'content' => $this->lang['strexecute'],
                    'attr'    => [
                        'href'   => [
                            'url'     => 'sql',
                            'urlvars' => [
                                'subject'   => 'history',
                                'nohistory' => 't',
                                'queryid'   => Decorator::field('queryid'),
                                'paginate'  => Decorator::field('paginate'),
                            ],
                        ],
                        'target' => 'detail',
                    ],
                ],
                'remove' => [
                    'content' => $this->lang['strdelete'],
                    'attr'    => [
                        'href' => [
                            'url'     => 'history',
                            'urlvars' => [
                                'action'  => 'confdelhistory',
                                'queryid' => Decorator::field('queryid'),
                            ],
                        ],
                    ],
                ],
            ];

            echo $this->printTable(
                $history,
                $columns,
                $actions,
                'history-history',
                $this->lang['strnohistory']
            );
        } else {
            echo "<p>{$this->lang['strnohistory']}</p>" . PHP_EOL;
        }

        $navlinks = [
            'refresh' => [
                'attr'    => [
                    'href' => [
                        'url'     => 'history',
                        'urlvars' => [
                            'action'   => 'history',
                            'server'   => $request_server,
                            'database' => $request_database,
                        ],
                    ],
                ],
                'content' => $this->lang['strrefresh'],
            ],
        ];

        if (!is_null($sessionHistory)
            && count($sessionHistory)) {
            $navlinks['download'] = [
                'attr'    => [
                    'href' => [
                        'url'     => 'history',
                        'urlvars' => [
                            'action'   => 'download',
                            'server'   => $request_server,
                            'database' => $request_database,
                        ],
                    ],
                ],
                'content' => $this->lang['strdownload'],
            ];
            $navlinks['clear'] = [
                'attr'    => [
                    'href' => [
                        'url'     => 'history',
                        'urlvars' => [
                            'action'   => 'confclearhistory',
                            'server'   => $request_server,
                            'database' => $request_database,
                        ],
                    ],
                ],
                'content' => $this->lang['strclearhistory'],
            ];
        }

        $this->printNavLinks($navlinks, 'history-history', get_defined_vars());
    }

    public function doDelHistory($qid, $confirm)
    {
        if ($confirm) {
            $this->printHeader($this->headerTitle(), $this->scripts);

            // Bring to the front always
            echo '<body onload="window.focus();">' . PHP_EOL;

            echo "<h3>{$this->lang['strdelhistory']}</h3>" . PHP_EOL;
            echo "<p>{$this->lang['strconfdelhistory']}</p>" . PHP_EOL;

            echo '<pre>', htmlentities($_SESSION['history'][$this->getRequestParam('server', null)][$this->getRequestParam('database', null)][$qid]['query'], ENT_QUOTES, 'UTF-8'), '</pre>';
            echo '<form action="' . \SUBFOLDER . '/src/views/history" method="post">' . PHP_EOL;
            echo '<input type="hidden" name="action" value="delhistory" />' . PHP_EOL;
            echo "<input type=\"hidden\" name=\"queryid\" value=\"${qid}\" />" . PHP_EOL;
            echo $this->misc->form;
            echo "<input type=\"submit\" name=\"yes\" value=\"{$this->lang['stryes']}\" />" . PHP_EOL;
            echo "<input type=\"submit\" name=\"no\" value=\"{$this->lang['strno']}\" />" . PHP_EOL;
            echo '</form>' . PHP_EOL;
        } else {
            unset($_SESSION['history'][$this->getRequestParam('server', null)][$this->getRequestParam('database', null)][$qid]);
        }
    }

    public function doClearHistory($confirm)
    {
        if ($confirm) {
            $this->printHeader($this->headerTitle(), $this->scripts);

            // Bring to the front always
            echo '<body onload="window.focus();">' . PHP_EOL;

            echo "<h3>{$this->lang['strclearhistory']}</h3>" . PHP_EOL;
            echo "<p>{$this->lang['strconfclearhistory']}</p>" . PHP_EOL;

            echo '<form action="' . \SUBFOLDER . '/src/views/history" method="post">' . PHP_EOL;
            echo '<input type="hidden" name="action" value="clearhistory" />' . PHP_EOL;
            echo $this->misc->form;
            echo "<input type=\"submit\" name=\"yes\" value=\"{$this->lang['stryes']}\" />" . PHP_EOL;
            echo "<input type=\"submit\" name=\"no\" value=\"{$this->lang['strno']}\" />" . PHP_EOL;
            echo '</form>' . PHP_EOL;
        } else {
            unset($_SESSION['history'][$this->getRequestParam('server', null)][$this->getRequestParam('database', null)]);
        }
    }

    public function doDownloadHistory()
    {
        header('Content-Type: application/download');
        $datetime = date('YmdHis');
        header("Content-Disposition: attachment; filename=history{$datetime}.sql");

        foreach ($_SESSION['history'][$this->getRequestParam('server', null)][$this->getRequestParam('database', null)] as $queries) {
            $query = rtrim($queries['query']);
            echo $query;
            if (';' != substr($query, -1)) {
                echo ';';
            }

            echo PHP_EOL;
        }
    }
}
