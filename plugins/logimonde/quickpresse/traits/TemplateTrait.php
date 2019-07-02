<?php namespace Logimonde\Quickpresse\Traits;

use Logimonde\QuickPresse\Classes\EblastFooter;

trait TemplateTrait
{
    public function basicHtml($campaign, $qp)
    {
        $data['host'] = $this->host;
        $this->page['qp'] = $data['qp'] = $qp;
        $this->page['campaign'] = $data['campaign'] = $campaign;
        $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
        $data['footer'] = EblastFooter::create($campaign);

        $view = \View::make('logimonde.quickpresse::layout.browser_preview', $data)
            ->nest('template', 'logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

        $this->page['template'] = $view;
    }

    public function basicFullWidth($campaign, $qp)
    {
        $data['host'] = $this->host;
        $this->page['qp'] = $data['qp'] = $qp;
        $this->page['campaign'] = $data['campaign'] = $campaign;
        $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
        $data['footer'] = EblastFooter::create($campaign);

        $view = \View::make('logimonde.quickpresse::layout.browser_preview', $data)
            ->nest('template', 'logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

        $this->page['template'] = $view;
    }

    public function logimonde($campaign, $qp)
    {
        $data['host'] = $this->host;
        $this->page['qp'] = $data['qp'] = $qp;
        $this->page['campaign'] = $data['campaign'] = $campaign;
        $data['fileHtml'] = $this->readHtmlFile($campaign->contents[0]->source_path);
        $view = \View::make('logimonde.quickpresse::layout.browser_preview_logimonde', $data)
            ->nest('template', 'logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

        $this->page['template'] = $view;
    }

    public function basicImage($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function oneColumn($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function oneColumnFull($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function twoColumns($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function twoColumnsFull($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function threeColumns($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function threeColumnsFull($campaign, $qp)
    {
        $this->columnShareMethod($campaign, $qp);
    }

    public function columnShareMethod($campaign, $qp)
    {
        $data['host'] = $this->host;
        $this->page['qp'] = $data['qp'] = $qp;
        $this->page['campaign'] = $data['campaign'] = $campaign;
        $data['footer'] = EblastFooter::create($campaign);

        $view = \View::make('logimonde.quickpresse::layout.browser_preview', $data)
            ->nest('template', 'logimonde.quickpresse::template.' . $campaign->template->email_file, $data)->render();

        $this->page['template'] = $view;
    }
}
