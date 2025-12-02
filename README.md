# CareerForge - IA para Otimização de Currículos e Recrutamento

## Projeto de Extensão em Sistemas Inteligentes - UNINOVE

### 📋 Descrição
O CareerForge é uma aplicação web inteligente que utiliza IA generativa (Google Gemini) para auxiliar tanto candidatos quanto recrutadores no processo de recrutamento. A plataforma oferece duas funcionalidades principais: otimização de currículos para candidatos e análise de adequação de candidatos para vagas específicas.

### ✨ Funcionalidades

#### 👤 Para Candidatos (Pessoa Física)
- **Análise Inteligente de Currículos**: Avaliação detalhada com pontuação automática
- **Sugestões de Melhoria**: Recomendações específicas baseadas em IA
- **Otimização em Tempo Real**: Reescrita automática do currículo
- **Pontos Fortes e Fracos**: Identificação de áreas de destaque e oportunidades
- **Recomendações por Área**: Personalização por segmento profissional

#### 🏢 Para Recrutadores (Pessoa Jurídica)
- **Análise de Adequação**: Comparação entre currículos e requisitos da vaga
- **Pontuação de Match**: Sistema de porcentagem de compatibilidade
- **Identificação de Gaps**: Detecção de lacunas de qualificação
- **Recomendações de Contratação**: Sugestões baseadas em análise completa
- **Perfil do Candidato**: Resumo inteligente das qualificações

### 🛠️ Tecnologias Utilizadas

**Frontend:**
- HTML5, CSS3 com animações avançadas
- JavaScript (ES6+)
- Font Awesome para ícones
- Google Fonts (Inter)

**Backend:**
- PHP 7.4+
- API Google Gemini (gemini-2.0-flash)

**Design:**
- Interface responsiva e moderna
- Animações suaves com CSS3
- Design system com variáveis CSS
- Gradientes e efeitos visuais

### 🚀 Como Executar o Projeto

1. **Pré-requisitos:**
   - Servidor web (Apache, Nginx, ou XAMPP/WAMP)
   - PHP 7.4 ou superior
   - Conexão com internet para acessar a API Gemini

2. **Configuração:**
   ```bash
   # Clone ou extraia os arquivos na pasta do servidor web
   # Configure sua chave da API Gemini no arquivo analyze.php
   # $GEMINI_API_KEY = 'SUA_CHAVE_AQUI';
   ```

3. **Execução:**
   - Acesse o arquivo `index.html` através do servidor web
   - Para testes locais: `http://localhost/projeto/`

4. **Configuração da API:**
   - Obtenha uma chave de API no [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Insira a chave na variável `$GEMINI_API_KEY` em `analyze.php`

### 📁 Estrutura do Projeto

```
careerforge/
│
├── index.html              # Interface principal
├── analyze.php            # Backend e integração com IA
├── uploads/               # Diretório para uploads temporários
└── README.md             # Este arquivo
```

### 🎨 Design e Interface

- **Interface Moderna**: Design limpo com gradientes e sombras
- **Responsividade**: Adaptação para desktop e mobile
- **Animações Suaves**: Transições e efeitos visuais
- **Feedback Visual**: Indicação clara do processo de análise
- **Modo Escuro/Claro**: Variáveis CSS para fácil customização

### 🔧 Funcionalidades Técnicas

1. **Upload Inteligente:**
   - Suporte a PDF, DOC, DOCX e TXT
   - Alternância entre upload e colagem de texto
   - Validação de tipo e tamanho de arquivo

2. **Processamento com IA:**
   - Análise contextual profunda
   - Geração de conteúdo personalizado
   - Formatação estruturada de resultados

3. **Exportação de Resultados:**
   - Copiar currículo otimizado
   - Download como arquivo TXT
   - Visualização formatada

### 👥 Integrantes do Grupo

**Ciência da Computação - UNINOVE**  

- **Giovanna Andrade Assenço** - Pesquisa e Documentação
- **Gusttavo Shinn Huei Nascimento Lee** - Pesquisa e Documentação
- **Julio Cesar Ferreira Da Silva** - Pesquisa e Documentação
- **Lucas Moreschi Guerra** - Desenvolvimento
- **Nathan Ferrari Corrêa Sousa** - Pesquisa e Documentação
- **Pedro Henrique Maciel Siqueira** - Pesquisa e Documentação
- **Victor Hugo Bueno de Sousa** - Pesquisa e Documentação

### 📝 Observações do Projeto

- **Ambiente de Desenvolvimento**: Projeto configurado para fácil implantação
- **API Externa**: Requer conexão com internet para funcionamento completo
- **Limitações de Upload**: Arquivos até 5MB, formatos específicos
- **Segurança**: Validação de arquivos e proteção básica implementada
- **Extensibilidade**: Código modular para futuras melhorias

### 🔮 Melhorias Futuras

1. **Implementação Real de Processamento de Arquivos:**
   - Integração com bibliotecas PDF e DOC
   - OCR para imagens de currículos

2. **Funcionalidades Adicionais:**
   - Banco de vagas integrado
   - Sistema de matchmaking candidato-vaga
   - Dashboard para recrutadores

3. **Tecnologias Avançadas:**
   - Machine Learning para análise preditiva
   - Integração com APIs de redes profissionais
   - Sistema de recomendação baseado em histórico

### 📄 Licença

Projeto acadêmico desenvolvido para a disciplina de Projeto de Extensão em Sistemas Inteligentes da UNINOVE.

### 🤝 Contribuições

Este é um projeto acadêmico, mas sugestões e melhorias são bem-vindas através de issues ou pull requests.

---

**UNINOVE - Universidade Nove de Julho**  
**Ciência da Computação**  
**Projeto de Extensão em Sistemas Inteligentes**  
**2025**

---
