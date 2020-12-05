<?php

namespace Think1st\TemplateEngine;

/*

  _   _     _       _   __     _   
 | | | |   (_)     | | /_ |   | |  
 | |_| |__  _ _ __ | | _| |___| |_ 
 | __| '_ \| | '_ \| |/ / / __| __|
 | |_| | | | | | | |   <| \__ \ |_ 
  \__|_| |_|_|_| |_|_|\_\_|___/\__|
                ultra mini runtime.   

*/

// Micro Twig Engine
class TwigEngine
{
    public $BASE_PATH;

    // Polyfill for startswith and endswith.
    private function startsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }
    private function endsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
    private function contains($haystack, $needle)
    {
        if (strpos($haystack, $needle) !== false) {
            return true;
        } else {
            return false;
        }
    }

    // Super small template engine. v1.0
    private function templateEngineRender($path, $variables)
    {
        if (file_exists($path)) {

            // Properly split and find out what is a command or not.
            $content = file_get_contents($path);
            $content = str_replace("{% ", "{%}-[-INT-]-", $content);
            $content = str_replace(" %}", "{%}", $content);

            // Interpret includes
            $parts = explode("{%}", $content);
            $result = "";
            foreach ($parts as $contentPart) {
                // See if we need to interpret something
                if (strpos($contentPart, '-[-INT-]-') !== false) {

                    // See what we need to do.
                    $toInterpret = str_replace("-[-INT-]-", "", $contentPart);

                    // Trim
                    $toInterpret = ltrim(rtrim($toInterpret));

                    // If 
                    if ($this->startsWith($toInterpret, "if")) {
                        $toInterpret = str_replace("if ", "", $toInterpret);
                        if ($this->contains($toInterpret, " == ")) {
                            $parts = explode(" == ", $toInterpret);
                            // Check on both value and boolean values!
                            if ($parts[1] == "false") {
                                if (isset($variables[$parts[0]]) && $variables[$parts[0]] == false) {
                                    $result .= "<!-- T1:IF -->";
                                } else {
                                    $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                                }
                            } elseif ($parts[1] == "true") {
                                if (isset($variables[$parts[0]]) && $variables[$parts[0]] == true) {
                                    $result .= "<!-- T1:IF -->";
                                } else {
                                    $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                                }
                            } elseif (isset($variables[$parts[0]]) && $variables[$parts[0]] == $parts[1]) {
                                $result .= "<!-- T1:IF -->";
                            } else {
                                $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                            }
                        } elseif ($this->contains($toInterpret, " != ")) {
                            $parts = explode(" != ", $toInterpret);
                            // Check on both value and boolean values!
                            if ($parts[1] == "false") {
                                if (isset($variables[$parts[0]]) && $variables[$parts[0]] != false) {
                                    $result .= "<!-- T1:IF -->";
                                } else {
                                    $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                                }
                            } elseif ($parts[1] == "true") {
                                if (isset($variables[$parts[0]]) && $variables[$parts[0]] != true) {
                                    $result .= "<!-- T1:IF -->";
                                } else {
                                    $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                                }
                            } elseif (isset($variables[$parts[0]]) && $variables[$parts[0]] != $parts[1]) {
                                $result .= "<!-- T1:IF -->";
                            } else {
                                $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                            }
                        } else {
                            if (isset($variables[$toInterpret]) && $variables[$toInterpret] == true) {
                                $result .= "<!-- T1:IF -->";
                            } else {
                                $result .= "<!-- T1:IF --><!-- T1:HIDE -->";
                            }
                        }
                    }

                    if ($this->startsWith($toInterpret, "endif")) {
                        $result .= "<!-- T1:IF -->";
                    }

                    if ($this->startsWith($toInterpret, "for")) {
                        $parts = explode("in", $toInterpret);
                        $arrayToInterpret = trim(str_replace("for ", "", $parts[0]));
                        $result .= "<!-- T1:FOR -->[-]" . $arrayToInterpret . "[-]" . $parts[1] . "[-]";
                    }

                    if ($this->startsWith($toInterpret, "endfor")) {
                        $result .= "<!-- T1:FOR -->";
                    }

                    // Include
                    if ($this->startsWith($toInterpret, "include")) {

                        // Get template.
                        $toInterpret = str_replace("include ", "", $toInterpret);
                        $toInterpret = str_replace("\"", "", $toInterpret);
                        $toInterpret = str_replace("'", "", $toInterpret);

                        // Add result to all results.
                        $result .= $this->templateEngineRender($this->BASE_PATH . ltrim(rtrim($toInterpret)), $variables);
                    }
                } else {
                    $result .= $contentPart;
                }
            }

            return $result;
        } else {
            return "";
        }
    }

    public function templateEngine($path, $variables)
    {
        // First include all render results, prepare for if!
        $result = $this->templateEngineRender($path, $variables);

        // New Result
        $newResult = "";
        $forParts = explode("<!-- T1:FOR -->", $result);
        if (is_array($forParts)) {
            foreach ($forParts as $key => $forPart) {
                if ($this->contains($forPart, "[-]")) {
                    // We do nothing!
                    $allParts = explode("[-]", $forPart);
                    if (is_array($allParts) && isset($variables[trim($allParts[2])])) {
                        foreach ($variables[trim($allParts[2])] as $key => $value) {
                            if (is_array($value)) {
                                $tmp = $allParts[3];
                                foreach ($value as $kv => $vv) {
                                    if ($vv != null && $vv != "") {
                                        $tmp = str_replace("{{ " . trim($allParts[1]) . "." . $kv . " }}", $vv, $tmp);
                                    }
                                }
                                $newResult .= $tmp;
                            } else {
                                if ($value != null && $value != "") {
                                    $newResult .= str_replace("{{ " . trim($allParts[1]) . " }}", $value, $allParts[3]);
                                }
                            }
                        }
                    }
                } else {
                    $newResult .= $forPart;
                }
            }
        }

        // Interpret if end result.
        $endResult = "";
        $ifParts = explode("<!-- T1:IF -->", $newResult);
        foreach ($ifParts as $key => $ifPart) {
            if ($this->contains($ifPart, "<!-- T1:HIDE -->")) {
                // We do nothing!
            } else {
                $endResult .= $ifPart;
            }
        }

        // Variables
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                // We do nothing at the moment
            } else {
                $endResult = str_replace("{{" . $key . "}}", $value, $endResult);
                $endResult = str_replace("{{ " . $key . " }}", $value, $endResult);
                $endResult = str_replace("{{" . $key . " }}", $value, $endResult);
                $endResult = str_replace("{{ " . $key . "}}", $value, $endResult);
            }
        }

        // Return if end result
        return $endResult;
    }
}
