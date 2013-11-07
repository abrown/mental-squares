<?php

/**
 * @copyright Copyright 2011 Andrew Brown. All rights reserved.
 * @license GNU/GPL, see 'help/LICENSE.html'.
 */
class Patient extends ResourceItem {

    public $first_name;
    public $last_name;
    public $age;
    public $address;
    protected $storage = array('type' => 'json', 'location' => 'data/patients.json');

    /**
     * Return a Patient even if no ID is supplied; this allows GET requests
     * with no ID to not return errors in a situation where an HTML client
     * wants to create a Patient with 'api.php/patient'
     * @return \Patient
     */
    public function GET() {
        if (!$this->getID()) {
            return $this;
        } else {
            return parent::GET();
        }
    }

    /**
     * Modify output representation
     * @param Representation $representation
     * @return Representation 
     */
    public function OUTPUT_TRIGGER(Representation $representation) {
        // switch from form data to HTML
        if ($representation->getContentType() == 'application/x-www-form-urlencoded') {
            $representation->setContentType('text/html');
        }
        // filter HTML responses on HTTP method
        if ($representation->getContentType() == 'text/html') {
            switch (WebHttp::getMethod()) {
                case 'GET':
                    $representation->setTemplate('template.php', WebTemplate::PHP_FILE);
                    $representation->getTemplate()->replaceFromPHPFile('main_view', 'views/patient.php', array('resource' => $this));
                    break;
                case 'OPTIONS':
                    $representation->setTemplate('template.php', WebTemplate::PHP_FILE);
                    $representation->getTemplate()->replaceFromPHPFile('main_view', 'views/options.php', array('resource' => $representation->getData()));
                    break;
                case 'PUT':
                case 'POST':
                case 'DELETE':
                    WebHttp::redirect(WebUrl::create('index.php'));
                    break;
            }
        }
        return $representation;
    }

}