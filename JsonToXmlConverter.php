<?php

class JsonToXmlConverter
{
    /**
     * Convierte un archivo JSON a XML y lo guarda en un archivo.
     *
     * @param string $jsonFilePath Ruta al archivo JSON.
     * @param string $xmlFilePath Ruta donde se guardará el archivo XML.
     * @return void
     * @throws Exception Si el archivo JSON no puede ser leído o si el contenido no es válido.
     */
    public static function convertAndSave($jsonFilePath, $xmlFilePath)
    {
        // Verifica si el archivo JSON existe
        if (!file_exists($jsonFilePath)) {
            throw new Exception("El archivo JSON no existe.");
        }

        // Carga el contenido del archivo JSON
        $jsonContent = file_get_contents($jsonFilePath);
        if ($jsonContent === false) {
            throw new Exception("No se pudo leer el archivo JSON.");
        }

        // Decodifica el contenido JSON a un array
        $arrayData = json_decode($jsonContent, true);
        if ($arrayData === null) {
            throw new Exception("El archivo JSON no es válido.");
        }

        // Convierte el array a XML
        $xmlContent = self::arrayToXml($arrayData, new SimpleXMLElement('<inventario/>'));

        // Guarda el XML en un archivo
        if ($xmlContent->asXML($xmlFilePath) === false) {
            throw new Exception("No se pudo guardar el archivo XML.");
        }

        echo "El archivo XML se ha guardado exitosamente en: <a href=".$xmlFilePath.">inventario.xml</a>\n";
    }

    /**
     * Convierte un array a un objeto SimpleXMLElement.
     *
     * @param array $data Array de datos para convertir.
     * @param SimpleXMLElement $xml Objeto SimpleXMLElement donde se añadirá el contenido.
     * @return SimpleXMLElement Objeto XML con los datos añadidos.
     */
    private static function arrayToXml(array $data, SimpleXMLElement $xml)
    {
        foreach ($data as $key => $value) {
            // Si el valor es un array, llama recursivamente
            if (is_array($value)) {
                $subNode = is_numeric($key) ? "producto" : $key; // Usar "producto" para listas numéricas
                self::arrayToXml($value, $xml->addChild($subNode));
            } else {
                // Añade el nodo al XML
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml;
    }
}

// Ejemplo de uso
try {
    $jsonFilePath = "inventario.json"; // Ruta al archivo JSON
    $xmlFilePath = "inventario_convertido.xml"; // Ruta donde se guardará el archivo XML

    // Convierte el archivo JSON a XML y lo guarda
    JsonToXmlConverter::convertAndSave($jsonFilePath, $xmlFilePath);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}