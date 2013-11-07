<?php

class Formatter extends WebPage {

    /**
     * Create a URL for use within this helper class
     * @param string $uri
     * @param string $method
     * @return string
     */
    public static function getResourceUrl($uri, $method = null) {
        $url = WebUrl::create('api.php/' . $uri);
        if ($method)
            $url .= '?method=' . strtoupper($method);
        return $url;
    }

    /**
     * Transform a property from something like 'last_name' to 'Last Name'
     * @param string $property
     * @return string
     */
    public static function makePropertyReadable($property) {
        $property = str_replace('_', ' ', $property);
        $property = ucwords($property);
        return $property;
    }

    /**
     * Make an HTML form for the Test class
     * @param Resource $resource
     */
    public static function makeTestForm(Test $resource) {
        // test questions
        $questions = array(
            'q1' => 'Maintains personal hygiene and appearance',
            'q2' => 'Is independent in transport and communication',
            'q3' => 'Provides adequate nourishment for self',
            'q4' => 'Attends to role performance tasks',
            'q5' => 'Effective in role performance',
            'q6' => 'Earns livelihood',
            'q7' => 'Communicates with family',
            'q8' => 'Participates in family activities',
            'q9' => 'Maintains good family relations',
            'q10' => 'Has network of friends',
            'q11' => 'Participates in social activities',
            'q12' => 'Behaves appropriately in social settings',
            'q13' => 'Able to self-monitor',
            'q14' => 'Exhibits good judgement',
            'q15' => 'Strives for good character and conduct',
            'q16' => 'Competence in verbal and linguistic tasks',
            'q17' => 'Competence in logical tasks',
            'q18' => 'Competence in numerical tasks',
            'q19' => 'Interest/appreciation in arts',
            'q20' => 'Interest/appreciation in the human condition'
        );
        // checked function
        $c = function ($a, $b) {
                    return ($a === $b) ? 'checked' : '';
                };
        // build HTML
        $uri = htmlentities($resource->getURI());
        $html = array();
        $html[] = "<form method='POST' action='" . self::getResourceUrl($uri) . "'>";
        // make patient selector
        $patients = new Patients();
        $patients->GET();
        $html[] = "<p>Patient: " . self::getResourceSelector($patients, $resource->patient) . "</p>";
        // build questions
        $html[] = "<table class='{$uri}'>";
        foreach ($questions as $property => $question) {
            $v = $resource->$property;
            $html[] = "<tr>";
            $html[] = "<td>{$property}. {$question}</td>";
            $html[] = "<td id='{$uri}#{$property}'>";
            $html[] = "<input type='radio' name='{$property}' value='0.0' {$c(0, $v)}/>1 ";
            $html[] = "<input type='radio' name='{$property}' value='0.2' {$c(0.2, $v)}/>2 ";
            $html[] = "<input type='radio' name='{$property}' value='0.4' {$c(0.4, $v)}/>3 ";
            $html[] = "<input type='radio' name='{$property}' value='0.6' {$c(0.6, $v)}/>4 ";
            $html[] = "<input type='radio' name='{$property}' value='0.8' {$c(0.8, $v)}/>5 ";
            $html[] = "</td></tr>";
        }
        // submit
        $html[] = "<tr>";
        $html[] = "<td></td>";
        $html[] = "<td><input type='submit' id='{$uri}#submit' value='Save' /></td>";
        $html[] = "</tr>";
        $html[] = "</table></form>";
        return implode("\n", $html);
    }

    /**
     * Return HTML links for each of the available methods in a Resource
     * @param Resource $resource
     * @return type 
     */
    public static function getResourceLinks(Resource $resource) {
        $uri = htmlentities($resource->getURI());
        $html = array();
        // GET
        $url = self::getResourceUrl($uri, 'GET');
        $html[] = "<a href='{$url}' title='{$uri}'>Edit</a>";
        // DELETE
        $url = self::getResourceUrl($uri, 'DELETE');
        $html[] = "<a href='{$url}' title='{$uri}'>Delete</a>";
        // return
        return implode("\n", $html);
    }

    /**
     * Return HTML list representing a ResourceList
     * @param ResourceList $list
     * @return string
     */
    public static function getResourceList(ResourceList $list) {
        $uri = htmlentities($list->getURI());
        $html = array();
        $html[] = "<table class='{$uri}'>";
        // head
        $class = $list->getItemType();
        $object = new $class;
        $html[] = "<tr class='head'>";
        foreach (get_public_vars($object) as $property => $value) {
            $_property = self::makePropertyReadable($property);
            $html[] = "<th>{$_property}</th>";
        }
        $html[] = "<th></th>";
        $html[] = "</tr>";
        // rows
        foreach ($list->items as $item) {
            $_uri = htmlentities($item->getURI());
            $html[] = "<tr>";
            foreach (get_public_vars($item) as $property => $value) {
                $property = htmlentities($property);
                $value = htmlentities($value);
                $html[] = "<td class='{$_uri}#{$property}'>{$value}</td>";
            }
            $html[] = "<td id='{$_uri}#links'>" . self::getResourceLinks($item) . "</td>";
            $html[] = "</tr>";
        }
        // submit
        $html[] = "</table>";
        return implode("\n", $html);
    }

    /**
     * Return list of
     * @param Tests $list
     * @return string
     */
    public static function getLatestTests(Tests $list) {
        $uri = htmlentities($list->getURI());
        $html = array();
        $html[] = "<table class='{$uri}'>";
        // head
        $class = $list->getItemType();
        $object = new $class;
        $html[] = "<tr class='head'><th>Patient</th><th>Modified On</th><th></tr>";
        // rows
        foreach ($list->items as $item) {
            $_uri = htmlentities($item->getURI());
            // get name
            $patient = new Patient($item->patient);
            $patient->GET();
            $name = htmlentities($patient->first_name . ' ' . $patient->last_name);
            // get modified date
            $modified = $item->modified;
            // get links
            $links = self::getResourceLinks($item);
            // to HTML
            $html[] = "<tr><td>$name</td><td>{$modified}</td><td>{$links}</td></tr>";
        }
        // submit
        $html[] = "</table>";
        return implode("\n", $html);
    }

    public static function getPatientTests(Patient $patient) {
        $tests = new Tests();
        $_GET['filter_on'] = 'patient';
        $_GET['filter_with'] = $patient->getURI();
        $tests->GET();
        // build HTML
        $uri = htmlentities($tests->getURI());
        $html = array();
        $html[] = "<table class='{$uri}'>";
        // head
        $class = $tests->getItemType();
        $object = new $class;
        $html[] = "<tr class='head'><th>Patient</th><th>Modified On</th><th></tr>";
        // rows
        foreach ($tests->items as $item) {
            $_uri = htmlentities($item->getURI());
            // get name
            $name = htmlentities($patient->first_name . ' ' . $patient->last_name);
            // get modified date
            $modified = $item->modified;
            // get links
            $links = self::getResourceLinks($item);
            // to HTML
            $html[] = "<tr><td>$name</td><td>{$modified}</td><td>{$links}</td></tr>";
        }
        // submit
        $html[] = "</table>";
        return implode("\n", $html);
    }

    /**
     * Return an HTML select element from a ResourceList; the selected value
     * will be the ResourceItem's URI
     * @param ResourceList $list
     */
    public static function getResourceSelector(ResourceList $list, $selected = null) {
        $list->GET();
        $uri = htmlentities($list->getURI());
        $item_type = strtolower($list->getItemType());
        $html = array();
        $html[] = "<select class='{$uri}' name='{$item_type}'>";
        foreach ($list->items as $id => $item) {
            $_uri = htmlentities($item->getURI());
            $name = htmlentities($item->first_name . ' ' . $item->last_name);
            if ($_uri == $selected) {
                $html[] = "<option id='{$_uri}#selector' value='{$_uri}' selected='selected'>{$name}</option>";
            } else {
                $html[] = "<option id='{$_uri}#selector' value='{$_uri}'>{$name}</option>";
            }
        }
        $html[] = "</select>";
        return implode("\n", $html);
    }

    /**
     * Return HTML form representing a Resource
     * @param Resource $resource
     * @return string
     */
    public static function getResourceForm(Resource $resource) {
        $uri = htmlentities($resource->getURI());
        $html = array();
        $html[] = "<form method='POST' action='" . self::getResourceUrl($uri) . "'>";
        $html[] = "<table class='{$uri}'>";
        foreach (get_public_vars($resource) as $property => $value) {
            $property = htmlentities($property);
            $_property = self::makePropertyReadable($property);
            $value = htmlentities($value);
            $html[] = "<tr>";
            $html[] = "<td class='{$uri}#property'>{$_property}</td>";
            $html[] = "<td id='{$uri}#{$property}'><input type='text' name='{$property}' value='{$value}' /></td>";
            $html[] = "</tr>";
        }
        // submit
        $html[] = "<tr>";
        $html[] = "<td></td>";
        $html[] = "<td><input type='submit' id='{$uri}#submit' value='Save' /></td>";
        $html[] = "</tr>";
        $html[] = "</table></form>";
        return implode("\n", $html);
    }

}