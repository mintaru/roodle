<?php

namespace App\Helpers;

class WordToHtmlConverter
{
    /**
     * Конвертирует Word документ в HTML с сохранением форматирования
     * 
     * @param string $filePath Путь к файлу Word
     * @return string HTML контент
     */
    public static function convert($filePath)
    {
        try {
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if ($fileExtension === 'docx') {
                return self::convertDocx($filePath);
            } elseif ($fileExtension === 'doc') {
                return self::convertDoc($filePath);
            } else {
                return '<p>Неподдерживаемый формат файла. Используйте .docx или .doc</p>';
            }
        } catch (\Exception $e) {
            return '<p>Ошибка при обработке документа: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }

    /**
     * Конвертирует DOCX файл в HTML
     * DOCX - это ZIP архив с XML файлами
     * 
     * @param string $filePath Путь к DOCX файлу
     * @return string HTML контент
     */
    private static function convertDocx($filePath)
    {
        // Открываем ZIP архив
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Не удалось открыть DOCX файл');
        }

        // Получаем содержимое документа (document.xml)
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) {
            return '<p>Не удалось извлечь содержимое из документа</p>';
        }

        // Парсим XML
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        
        if (!@$dom->loadXML($xml)) {
            return '<p>Ошибка при чтении XML документа</p>';
        }

        // Конвертируем в HTML
        return self::xmlToHtml($dom);
    }

    /**
     * Конвертирует DOC файл в HTML
     * DOC - бинарный формат, читаем только текст
     * 
     * @param string $filePath Путь к DOC файлу
     * @return string HTML контент
     */
    private static function convertDoc($filePath)
    {
        $text = '';
        
        try {
            // Открываем файл в бинарном режиме
            $handle = fopen($filePath, 'rb');
            if (!$handle) {
                throw new \Exception('Не удалось открыть файл');
            }

            $content = fread($handle, filesize($filePath));
            fclose($handle);

            // Ищем текст в документе (очень примитивное решение)
            // DOC формат содержит текст в utf-16, но часто также в utf-8
            $text = preg_replace("/[^\x20-\x7E\xA0-\xFF\n\r]/", '', $content);
            
            // Более надежный способ - использовать phpword если доступна
            if (class_exists('\PhpOffice\PhpWord\IOFactory')) {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                return self::phpWordToHtml($phpWord);
            }

            // Если PHPWord недоступна, конвертируем простой текст
            if (empty($text)) {
                return '<p>Не удалось извлечь текст из DOC файла. Попробуйте конвертировать в DOCX.</p>';
            }

            // Преобразуем текст в параграфы
            $text = htmlspecialchars($text);
            $paragraphs = preg_split('/[\n\r]+/', $text);
            $html = '';
            
            foreach ($paragraphs as $p) {
                $p = trim($p);
                if (!empty($p)) {
                    $html .= '<p>' . $p . '</p>';
                }
            }

            return $html ?: '<p>Документ пуст</p>';
        } catch (\Exception $e) {
            return '<p>Ошибка при обработке DOC файла: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }

    /**
     * Конвертирует DOMDocument (из DOCX) в HTML
     * 
     * @param \DOMDocument $dom
     * @return string HTML
     */
    private static function xmlToHtml(\DOMDocument $dom)
    {
        $xpath = new \DOMXPath($dom);
        
        // Регистрируем namespace для Word документов
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        
        $html = '';
        
        // Получаем все параграфы
        $paragraphs = $xpath->query('//w:p', $dom->documentElement);
        
        foreach ($paragraphs as $paragraph) {
            $para_text = self::extractParagraphText($paragraph, $xpath);
            if (trim($para_text) !== '') {
                $html .= '<p>' . $para_text . '</p>';
            }
        }

        // Получаем таблицы
        $tables = $xpath->query('//w:tbl', $dom->documentElement);
        foreach ($tables as $table) {
            $html .= self::extractTableHtml($table, $xpath);
        }

        return $html ?: '<p>Документ пуст</p>';
    }

    /**
     * Извлекает текст из параграфа с форматированием
     * 
     * @param \DOMElement $paragraph
     * @param \DOMXPath $xpath
     * @return string HTML текст параграфа
     */
    private static function extractParagraphText(\DOMElement $paragraph, \DOMXPath $xpath)
    {
        $text = '';
        
        // Получаем все текстовые элементы (runs)
        $runs = $xpath->query('.//w:r', $paragraph);
        
        foreach ($runs as $run) {
            $text .= self::extractRunText($run, $xpath);
        }

        return $text;
    }

    /**
     * Извлекает текст из run элемента с форматированием
     * 
     * @param \DOMElement $run
     * @param \DOMXPath $xpath
     * @return string HTML текст
     */
    private static function extractRunText(\DOMElement $run, \DOMXPath $xpath)
    {
        $textElements = $xpath->query('.//w:t', $run);
        $text = '';
        
        foreach ($textElements as $elem) {
            $text .= htmlspecialchars($elem->nodeValue);
        }

        if (empty($text)) {
            return '';
        }

        // Проверяем форматирование (w:rPr - run properties)
        $rpr = $xpath->query('.//w:rPr', $run)->item(0);
        
        if ($rpr) {
            // Жирный текст
            if ($xpath->query('.//w:b', $rpr)->length > 0) {
                $text = '<strong>' . $text . '</strong>';
            }
            
            // Курсив
            if ($xpath->query('.//w:i', $rpr)->length > 0) {
                $text = '<em>' . $text . '</em>';
            }
            
            // Подчеркивание
            if ($xpath->query('.//w:u', $rpr)->length > 0) {
                $text = '<u>' . $text . '</u>';
            }

            // Зачёркивание
            if ($xpath->query('.//w:strike', $rpr)->length > 0) {
                $text = '<s>' . $text . '</s>';
            }

            // Цвет текста
            $colorElement = $xpath->query('.//w:color', $rpr)->item(0);
            if ($colorElement && $colorElement->hasAttribute('w:val')) {
                $color = $colorElement->getAttribute('w:val');
                if ($color && $color !== 'auto') {
                    $text = '<span style="color: #' . htmlspecialchars($color) . '">' . $text . '</span>';
                }
            }

            // Размер шрифта (в половинах пункта)
            $sizeElement = $xpath->query('.//w:sz', $rpr)->item(0);
            if ($sizeElement && $sizeElement->hasAttribute('w:val')) {
                $size = intval($sizeElement->getAttribute('w:val')) / 2;
                $text = '<span style="font-size: ' . $size . 'pt">' . $text . '</span>';
            }
        }

        return $text;
    }

    /**
     * Извлекает HTML таблицу из Word таблицы
     * 
     * @param \DOMElement $table
     * @param \DOMXPath $xpath
     * @return string HTML таблица
     */
    private static function extractTableHtml(\DOMElement $table, \DOMXPath $xpath)
    {
        $html = '<table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">';
        
        $rows = $xpath->query('.//w:tr', $table);
        foreach ($rows as $row) {
            $html .= '<tr>';
            
            $cells = $xpath->query('.//w:tc', $row);
            foreach ($cells as $cell) {
                $html .= '<td style="border: 1px solid #ddd; padding: 8px;">';
                
                // Извлекаем все параграфы из ячейки
                $paragraphs = $xpath->query('.//w:p', $cell);
                foreach ($paragraphs as $paragraph) {
                    $para_text = self::extractParagraphText($paragraph, $xpath);
                    if (trim($para_text) !== '') {
                        $html .= '<p>' . $para_text . '</p>';
                    }
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table><br>';
        return $html;
    }

    /**
     * Конвертирует PHPWord объект в HTML (если PHPWord доступна)
     * 
     * @param mixed $phpWord
     * @return string HTML
     */
    private static function phpWordToHtml($phpWord)
    {
        $html = '';
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $elementClass = get_class($element);
                
                if (strpos($elementClass, 'Paragraph') !== false) {
                    $html .= '<p>';
                    foreach ($element->getElements() as $child) {
                        $html .= self::phpWordElementToHtml($child);
                    }
                    $html .= '</p>';
                } elseif (strpos($elementClass, 'Table') !== false) {
                    $html .= '<table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">';
                    
                    foreach ($element->getRows() as $row) {
                        $html .= '<tr>';
                        foreach ($row->getCells() as $cell) {
                            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">';
                            foreach ($cell->getElements() as $child) {
                                $html .= self::phpWordElementToHtml($child);
                            }
                            $html .= '</td>';
                        }
                        $html .= '</tr>';
                    }
                    
                    $html .= '</table>';
                }
            }
        }
        
        return $html ?: '<p>Документ пуст</p>';
    }

    /**
     * Конвертирует элемент PHPWord в HTML
     * 
     * @param mixed $element
     * @return string HTML
     */
    private static function phpWordElementToHtml($element)
    {
        $elementClass = get_class($element);
        
        if (strpos($elementClass, 'Text') !== false) {
            $text = htmlspecialchars($element->getText());
            if (method_exists($element, 'getStyle')) {
                $style = $element->getStyle();
                if ($style && method_exists($style, 'isBold') && $style->isBold()) {
                    $text = '<strong>' . $text . '</strong>';
                }
                if ($style && method_exists($style, 'isItalic') && $style->isItalic()) {
                    $text = '<em>' . $text . '</em>';
                }
            }
            return $text;
        }
        
        return '';
    }
}
