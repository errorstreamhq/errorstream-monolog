<?php

namespace ErrorStream\ErrorStreamMonologHandler;
use ErrorStream\ErrorStreamClient\ErrorStreamClient;
use ErrorStream\ErrorStreamClient\ErrorStreamReport;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class ErrorStreamMonologHandler extends AbstractProcessingHandler
{
    public $api_token;
    public $project_token;

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        switch($record['level'])
        {
            case Logger::DEBUG:
            case Logger::INFO:
            case Logger::NOTICE:
                $severity = 1;
                break;
            case Logger::WARNING:
                $severity = 2;
                break;
            default:
                $severity = 3;
                break;
        }

        $report = new ErrorStreamReport();
        $report->error_group = $record['formatted'];
        $report->line_number = 0;
        $report->file_name = 'Monolog';
        $report->message = $record['formatted'];
        $report->stack_trace = $record['formatted'];
        $report->severity = $severity;

        $client = new ErrorStreamClient();
        $client->api_token = $this->api_token;
        $client->project_token = $this->project_token;
        $client->reportException($report);
    }

}