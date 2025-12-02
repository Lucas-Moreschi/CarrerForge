<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$max_file_size = 5 * 1024 * 1024;
$allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
$allowed_extensions = ['pdf', 'doc', 'docx', 'txt'];
$upload_dir = 'uploads/';

// CONFIGURAÇÃO DA API GEMINI - INSIRA SUA CHAVE AQUI
// Obtenha uma chave gratuita em: https://console.cloud.google.com/
$GEMINI_API_KEY = ''; // INSIRA SUA CHAVE API AQUI
$GEMINI_MODEL = 'gemini-2.0-flash';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

function returnError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

function extractTextFromFile($file_path, $file_type) {
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'txt') {
            $content = file_get_contents($file_path);
            return "CONTEÚDO EXTRAÍDO DO ARQUIVO TXT:\n\n" . $content;
        } elseif ($extension === 'pdf') {
            $sample_text = file_exists($file_path) ? 
                "PDF enviado: " . basename($file_path) . " (" . filesize($file_path) . " bytes)\n\n" : 
                "";
            
            return $sample_text . 
                   "EM AMBIENTE DE PRODUÇÃO:\n" .
                   "1. Instale uma biblioteca de extração de PDF\n" .
                   "2. Use a opção 'Colar Texto' para testar agora.\n";
        } elseif ($extension === 'doc' || $extension === 'docx') {
            $sample_text = file_exists($file_path) ? 
                "Documento Word enviado: " . basename($file_path) . " (" . filesize($file_path) . " bytes)\n\n" : 
                "";
            
            return $sample_text . 
                   "EM AMBIENTE DE PRODUÇÃO:\n" .
                   "1. Instale a biblioteca PHPWord\n" .
                   "2. Use a opção 'Colar Texto' para testar agora.\n";
        }
    } catch (Exception $e) {
        return "Erro ao processar arquivo.\nUse a opção 'Colar Texto' para testar.\n";
    }
    
    return "Conteúdo do arquivo " . basename($file_path) . "\n\n";
}

function callGeminiAPI($prompt) {
    global $GEMINI_API_KEY, $GEMINI_MODEL;
    
    if (empty($GEMINI_API_KEY)) {
        throw new Exception('Chave da API Gemini não configurada');
    }
    
    $url = "https://generativelanguage.googleapis.com/v1/models/{$GEMINI_MODEL}:generateContent?key={$GEMINI_API_KEY}";
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ]
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            throw new Exception('Erro na comunicação com a API Gemini');
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            throw new Exception('Erro da API: ' . $data['error']['message']);
        }
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        } else {
            throw new Exception('Resposta da API inválida');
        }
        
    } catch (Exception $e) {
        error_log("Erro Gemini API: " . $e->getMessage());
        throw new Exception('Falha na comunicação com a IA');
    }
}

function analyzePFWithAI($content, $area = '') {
    $prompt = "Analise o seguinte currículo profissional e forneça uma análise detalhada e personalizada em português do Brasil.
    
DIRETRIZES:
- Seja específico, construtivo e profissional
- Analise o conteúdo real do currículo fornecido
- Identifique pontos fortes genuínos baseados no conteúdo
- Sugira melhorias práticas e acionáveis
- Forneça recomendações personalizadas
- Dê uma pontuação de 0-100 baseada na qualidade geral e potencial de mercado
- Seja honesto mas encorajador

" . ($area ? "ÁREA DE ATUAÇÃO ESPECÍFICA: $area\n" : "") . "

CONTEÚDO DO CURRÍCULO:
\"\"\"
{$content}
\"\"\"

Forneça a resposta APENAS em formato JSON com a seguinte estrutura:

{
    \"score\": 85,
    \"score_description\": \"Descrição clara da pontuação em português\",
    \"summary\": \"Resumo geral personalizado\",
    \"strengths\": [
        {
            \"title\": \"Título do ponto forte\",
            \"description\": \"Descrição específica\",
            \"content\": \"Explicação detalhada\"
        }
    ],
    \"suggestions\": [
        {
            \"title\": \"Título da sugestão\", 
            \"description\": \"Descrição clara\",
            \"content\": \"Explicação com exemplos\"
        }
    ],
    \"optimizations\": [
        {
            \"title\": \"Título da otimização\",
            \"description\": \"Descrição da otimização\",
            \"content\": \"Explicação com exemplos práticos\"
        }
    ],
    \"recommendations\": [
        {
            \"title\": \"Título da recomendação\",
            \"description\": \"Descrição da recomendação\",
            \"content\": \"Explicação detalhada\"
        }
    ]
}

Responda APENAS com o JSON, sem texto adicional.";

    $response = callGeminiAPI($prompt);
    
    if ($response) {
        preg_match('/\{(?:[^{}]|(?R))*\}/s', $response, $matches);
        if (isset($matches[0])) {
            $analysis = json_decode($matches[0], true);
            
            if (isset($analysis['score']) && isset($analysis['score_description'])) {
                return $analysis;
            }
        }
    }
    
    throw new Exception('Não foi possível processar a análise da IA');
}

function analyzePJWithAI($content, $specs = '', $area = '') {
    $prompt = "Analise a adequação do candidato para a vaga com base no currículo e nas especificações fornecidas. Responda em português do Brasil.

ESPECIFICAÇÕES DA VAGA:
\"\"\"
{$specs}
\"\"\"

CURRÍCULO DO CANDIDATO:
\"\"\"
{$content}
\"\"\"

" . ($area ? "ÁREA DA VAGA: $area\n" : "") . "

Forneça uma análise detalhada incluindo:
- Pontuação de adequação (0-100)
- Perfil resumido do candidato
- Análise de correspondência com critérios
- Qualificações relevantes
- Gaps identificados
- Recomendação final prática

Forneça a resposta APENAS em formato JSON:

{
    \"score\": 75,
    \"score_description\": \"Descrição da adequação\",
    \"profile\": \"Perfil resumido do candidato\",
    \"match_analysis\": [
        {
            \"title\": \"Critério analisado\",
            \"match\": \"Alta/Média/Baixa\",
            \"matchClass\": \"match-high/match-medium/match-low\",
            \"description\": \"Descrição da correspondência\"
        }
    ],
    \"qualifications\": [
        {
            \"title\": \"Título da qualificação\",
            \"description\": \"Descrição breve\",
            \"content\": \"Explicação detalhada\"
        }
    ],
    \"gaps\": [
        {
            \"title\": \"Título do gap\",
            \"description\": \"Descrição breve\",
            \"content\": \"Explicação e impacto\"
        }
    ],
    \"recommendation\": \"Recomendação final\"
}

Responda APENAS com o JSON, sem texto adicional.";

    $response = callGeminiAPI($prompt);
    
    if ($response) {
        preg_match('/\{(?:[^{}]|(?R))*\}/s', $response, $matches);
        if (isset($matches[0])) {
            $analysis = json_decode($matches[0], true);
            
            if (isset($analysis['score']) && isset($analysis['score_description'])) {
                return $analysis;
            }
        }
    }
    
    throw new Exception('Não foi possível processar a análise da IA');
}

function generateImprovedCVWithAI($originalCV, $analysis, $area = '') {
    $prompt = "Com base na análise abaixo, revise e melhore o seguinte currículo. Mantenha o português do Brasil.

ANÁLISE REALIZADA:
\"\"\"
" . json_encode($analysis, JSON_PRETTY_PRINT) . "
\"\"\"

CURRÍCULO ORIGINAL:
\"\"\"
{$originalCV}
\"\"\"

INSTRUÇÕES:
- Mantenha todas as informações importantes e verdadeiras
- Melhore a formatação, estrutura e organização
- Use linguagem mais impactante e profissional
- Não invente informações que não estão no original
- Mantenha os níveis de idioma EXATAMENTE como no original
- Destaque o que JÁ EXISTE de forma mais impactante

Forneça APENAS o currículo revisado e melhorado, sem comentários adicionais.";

    return callGeminiAPI($prompt);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    returnError('Método não permitido');
}

$type = $_POST['type'] ?? '';
$area = $_POST['area'] ?? '';
$specs = $_POST['specs'] ?? '';
$cv_text = $_POST['cv_text'] ?? '';

if (!in_array($type, ['pf', 'pj'])) {
    returnError('Tipo de análise inválido');
}

$file_path = null;
$cv_content = '';

if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['cv_file'];
    
    if ($file['size'] > $max_file_size) {
        returnError('Arquivo muito grande. Tamanho máximo: 5MB');
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file['type'], $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
        returnError('Tipo de arquivo não suportado. Use PDF, DOC, DOCX ou TXT');
    }

    $file_name = uniqid() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        returnError('Erro ao salvar o arquivo');
    }
    
    $cv_content = extractTextFromFile($file_path, $file['type']);
    
    if (strlen(trim($cv_content)) < 20) {
        returnError('Não foi possível extrair texto suficiente do arquivo. Use a opção "Colar Texto".');
    }
    
} elseif (!empty($cv_text)) {
    $cv_content = $cv_text;
    
    if (strlen(trim($cv_content)) < 50) {
        returnError('O texto do currículo deve ter pelo menos 50 caracteres.');
    }
} else {
    returnError('Nenhum currículo enviado.');
}

try {
    if ($type === 'pf') {
        $analysis = analyzePFWithAI($cv_content, $area);
        
        try {
            $improvedCV = generateImprovedCVWithAI($cv_content, $analysis, $area);
            $analysis['improved_cv'] = $improvedCV;
        } catch (Exception $e) {
            $analysis['improved_cv'] = $cv_content;
        }
        
    } else {
        if (empty($specs)) {
            returnError('Por favor, descreva o que você busca no candidato.');
        }
        
        $analysis = analyzePJWithAI($cv_content, $specs, $area);
    }
    
    echo json_encode($analysis);
    
} catch (Exception $e) {
    returnError('Erro durante a análise: ' . $e->getMessage());
}

if ($file_path && file_exists($file_path)) {
    unlink($file_path);
}
?>