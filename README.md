# SigilEngine - Opensource Blog software
Software de blog pessoal inspirado no antigo Blogspot, feito em HTML, CSS, JavaScript, PHP e Mysql


## Instalação Rápida

### 1. Preparação

- Copie a pasta `SigilEngine` para o diretório público do seu servidor web
- Certifique-se de que PHP 7.4+ está instalado
- MySQL/MariaDB deve estar acessível

### 2. Setup via Web (Recomendado)

1. Abra `http://seusite.com/setup.php` no navegador
2. Preencha as informações solicitadas:
   - Host MySQL (padrão: `localhost`)
   - Nome do banco de dados (padrão: `castlechan`)
   - Usuário e senha MySQL
   - Credenciais do administrador
   - Nome e descrição do site
3. Clique em "Executar Setup"

### 3. Setup via CLI (Alternativo)

Execute o script via terminal:

```bash
php setup.php
```

## Após o Setup

- Acesse o site em `http://seusite.com`
- Acesse o painel administrativo em `http://seusite.com/admin/login.php`
- Use as credenciais criadas no setup para fazer login

## Estrutura de Arquivos

```
castlechan/
├── setup.php              # Script de configuração (execute uma vez)
├── database.sql           # Estrutura completa do banco de dados
├── restore_database.php   # Restaurar banco para estado inicial
├── change_admin.php       # Alterar credenciais via CLI
├── config.php             # Configurações (gerado pelo setup)
├── index.php              # Página principal
├── post.php               # Visualização de posts
├── react.php              # Sistema unificado de like/dislike
├── like.php               # Atalho para curtidas (usa react.php)
├── dislike.php            # Atalho para dislikes (usa react.php)
├── includes/
│   ├── functions.php      # Funções compartilhadas
│   └── footer.php         # Rodapé (incluído em todas as páginas visitáveis)
├── admin/
│   ├── login.php          # Login administrativo
│   ├── dashboard.php      # Painel inicial
│   ├── posts.php          # Gerenciar posts
│   ├── post_edit.php      # Editar post
│   ├── categories.php     # Categorias
│   ├── banners.php        # Gerenciar banners
│   ├── users.php          # Gerenciar usuários
│   ├── settings.php       # Configurações do site
│   └── logout.php         # Logout
├── assets/
│   ├── style.css          # Estilos web1.0
│   └── script.js          # Scripts
└── uploads/               # Pasta de uploads (criada automaticamente)
```

## Recursos

- Painel administrativo com login
- Criar e editar posts com upload múltiplo de imagens
- Categorias para separar posts
- Customizar logo e favicon
- Sistema de reações: curtir e não gostei (dislikes) com proteção por cookie
- Comentários anônimos em posts
- Banners posicionais (esquerda, direita, topo) com URLs customizáveis
  - Banners laterais: 240px × 600px
  - Banner topo (skyscraper): 768px × 300px
- Rodapé compartilhado com copyright e licença GNU GPL 3.0
- Gerenciar múltiplos usuários
- Estilo web1.0 (Blogspot 2005)

## Gerenciar Credenciais

### Via Painel Administrativo

1. Faça login em `admin/login.php`
2. Clique em "Usuários" no menu lateral
3. Altere a senha ou nome de usuário
4. Crie novos usuários se necessário

### Via Script CLI

Execute o script PHP via linha de comando:

```bash
php change_admin.php
```

Este script oferece as seguintes opções:
- Alterar senha de usuário existente
- Criar novo usuário
- Listar todos os usuários
- Excluir usuário

## Restaurar Banco de Dados

### Opção 1: Via Script PHP (Recomendado)

```bash
php restore_database.php
```

Este script:
- Remove todos os posts, comentários e imagens
- Remove todos os banners
- Remove todas as categorias
- Mantém usuários e configurações intactas
- Não deleta arquivos da pasta `uploads/`

### Opção 2: Reconfigura Completo

Se precisar reconfigurar tudo:

1. Delete o arquivo `.setup-done` na raiz do projeto
2. Execute `setup.php` novamente

### Opção 3: Restaurar Manualmente via MySQL

```bash
mysql -u root -p castlechan < database.sql
```

Ou via phpMyAdmin:
1. Abra phpMyAdmin
2. Selecione o banco `castlechan`
3. Vá para "Import"
4. Selecione `database.sql`
5. Clique em "Go"

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou MariaDB 10.2
- Suporte a PDO (já vem com PHP)
- Extensão GD do PHP (opcional, para processamento de imagens)

## Segurança

- Altere as credenciais de admin imediatamente após o setup
- Use senhas fortes (mínimo 6 caracteres)
- Mantenha `config.php` fora do controle de versão
- Configure permissões corretas para a pasta `uploads/`
- Use HTTPS em produção

## Sistema de Banners

Os banners podem ser posicionados em três locais:

1. **Lado Esquerdo (240×600px)**: Exibe até 2 banners aleatórios
2. **Lado Direito (240×600px)**: Exibe até 2 banners aleatórios
3. **Topo (768×300px)**: Exibe 1 banner aleatório (skyscraper)

Características:
- URL customizável por banner (opcional)
- Ativação/desativação rápida
- Banners sem imagem não são exibidos
- Upload no painel administrativo (Admin → Banners)

## Sistema de Reações

Os posts podem receber:
- **Curtidas**: Incrementa contador de likes
- **Dislikes**: Incrementa contador de "não gostei"

Proteção contra votação repetida:
- Usa cookies com validade de 30 dias
- Uma reação por post por usuário

## Licença

Copyright © 2026 Castle of illusions gang

Powered by [SigilEngine 2026](https://github.com/IESXS/Sigil-Engine---blog-software)

Licensed under [GNU GPL 3.0](https://www.gnu.org/licenses/gpl-3.0.html)

## Suporte

Para questões ou problemas, verifique:
1. Se o banco de dados está acessível
2. Se o PHP tem as extensões necessárias
3. Se a pasta `uploads/` tem permissão de escrita
4. Se as credenciais do banco estão corretas em `config.php`


**Exemplo de uso:**
```bash
php change_admin.php
# Selecione opção 1 para alterar senha
# Digite o nome de usuário
# Digite a nova senha
```
