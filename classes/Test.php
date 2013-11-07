<?php

class Test extends ResourceItem {

    public $patient;
    public $modified;
    public $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $q11, $q12, $q13, $q14, $q15, $q16, $q17, $q18, $q19, $q20;
    protected $storage = array('type' => 'json', 'location' => 'data/tests.json');

    /**
     * Return a  even if no ID is supplied; this allows GET requests
     * with no ID to not return errors in a situation where an HTML client
     * wants to create a Test with 'api.php/patient'
     * @return \Test
     */
    public function GET() {
        if (!$this->getID()) {
            return $this;
        } else {
            // change date format
            $test = parent::GET();
            $test->modified = date('g:ia F j, Y', $test->modified);
            return $test;
        }
    }

    /**
     * Modify output representation
     * @param Representation $representation
     * @return Representation 
     */
    public function INPUT_TRIGGER(Representation $representation) {
        if(WebHttp::getMethod() == 'PUT' || WebHttp::getMethod() == 'POST'){
            $representation->getData()->modified = time();
        }
        return $representation;
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
                    $representation->getTemplate()->replaceFromPHPFile('main_view', 'views/test.php', array('resource' => $this));
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