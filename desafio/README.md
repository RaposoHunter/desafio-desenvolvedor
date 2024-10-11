# Oliveira Trust - Desafio Técnico Backend

<style>
    .endpoint {
        display: flex;
        flex-direction: column;
        gap: 0.5rem
    }

    .endpoint-method {
        padding: 0.3rem 1rem;
        border-radius: 16px;
        color: white;
        font-size: 0.8rem;
    }

    .endpoint-method.endpoint-get {
        background-color: #0e9b71;
    }

    .endpoint-method.endpoint-get::after {
        content: 'GET'
    }

    .endpoint-method.endpoint-post {
        background-color: #0171c2;
    }

    .endpoint-method.endpoint-post::after {
        content: 'POST'
    }

    th {
        text-align: center
    }
</style>

<div style="display:flex; justify-content: center; align-items: center">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQIAOtqQ5is5vwbcEn0ZahZfMxz1QIeAYtFfnLdkCXu1sqAGbnX" width="300">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</div>

## Sobre o desafio
O desafio consiste em desenvolver uma API para receber arquivos CSV e Excel, permitindo a consulta dos dados dos mesmos. A API foi construida utilizando o framkework PHP Laravel na sua versão mais atualizada no momento (11.26)

## Requisitos
* PHP 8.2+
* Laravel 11+
* Laravel MongoDB 5+
* Laravel Excel 3.1+

## Instalação

As dependências do projeto são gerenciadas pelo Composer e podem ser instaladas através do comando:
```bash
composer install
```

### Variáveis de ambiente
Após instalar as dependências será necessário criar o arquivo `.env`. Para isso, basta criar uma cópia do arquivo `.env.example` e renomear.

Além disso é necessário definir uma chave criptografica para que as senhas dos usuários estejam protegidas. Para isso basta executar o comando:
```bash
php artisan key:generate --ansi
```

### MongoDB
Talvez seja necessário instalar a extensão do MongoDB (caso ainda não a tenha no sistema). Para isso basta seguir o tutorial disponibilizado pelo PHP neste [link](https://www.php.net/manual/en/mongodb.installation.php).

## Iniciando o projeto
O Laravel oferece um servidor local próprio que pode ser utilizado através do comando:
```bash
php artisan serve --port=8080
```

Caso, por algum motivo, não seja possível iniciar um servidor local utilizando o comando acima, utilize a maneira nativa do PHP utilizando o comando:
```bash
php -S localhost:8080 -t public/
```

OBS: Caso porta 8080 esteja em uso basta informar uma outra. Neste caso, considere a nova porta nos exemplos abaixo.

## Endpoints
Para acessar a documentação completa da API, com exemplos, clique [aqui](https://www.postman.com/docking-module-geologist-46239461/1010229a-4f88-4d41-8f37-c018dd7886f7/documentation/mdfdcbn/desafio-tcnico-oliveira-trust).

### Login
<span class="endpoint">
    <span>
        <span class="endpoint-method endpoint-post"></span> /login
    </span>
    <span>
        Este endpoint permite que os usuário se autentiquem com a API antes de utilizar qualquer outro endpoint. Ao efetuar o login o token deve ser salvo e enviado em requisições subsequentes no modelo Bearer Auth.
    </span>
</span>

#### Exemplo em JavaScript

```js
// Passo 1: Montar as credenciais a serem usadas
const credentials = {
    email: "admin@oliveiratrust.com",
    password: "admin123"
};

// Passo 2: Efetuar chamada ao endpoint
const response = await fetch('https://localhost:8080/login', {
    method: 'POST',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(credentials)
});

// Passo 3: Obter a resposta em JSON
const { token } = await response.json();

// Passo 4: Salvar o token para requisições futuras
localStorage.setItem('token', token);
```

### Logout
<span class="endpoint">
    <span>
        <span class="endpoint-method endpoint-post"></span> /logout
    </span>
    <span>
        Este endpoint permite que os usuário autenticados finalizem suas sessões. Após realizar o logout é importante remover invalidar/limpar o token salvo.
    </span>
</span>

#### Exemplo em JavaScript

```js
// Passo 1: Efetuar chamada ao endpoint
const response = await fetch('https://localhost:8080/logout', {
    method: 'POST',
    headers: {
        Authorization: `Bearer ${token}`
    }
});

// Passo 2: Remover o token salvo
localStorage.removeItem('token');
```

### Upload de Arquivo
<span class="endpoint">
    <span>
        <span class="endpoint-method endpoint-post"></span> /api/v1/files
    </span>
    <span>
        Este endpoint permite realizar o upload de um arquivo CSV (.csv) ou Excel (.xlsx). Ao enviar o arquivo, ele será salvo no servidor e será iniciado um processo de importação para capturar os dados, salvando-os no banco de dados.
    </span>
</span>

#### Exemplo em JavaScript
```js
// Passo 1: Recuperar o token retornado pelo endpoint de login
const token = localStorage.getItem('token'); 

// Passo 2: Montar os dados a serem enviados ao endpoint
const form = document.getElementById('meu-form');
const formData = new FormData();
formData.append('file', form.file.files[0]); // obrigatório
formData.append('name', form.filename); // opcional

// Passo 3: Efetuar chamada ao endpoint
const response = await fetch('https://localhost:8080/api/v1/files', {
    method: 'POST',
    headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
    },
    body: formData
});

// Passo 4: Obter a resposta em JSON
const data = await response.json();
```

#### Resposta esperada em `data`
```json
{
    "data": {
        "name": "Nome informado ou nome do arquivo",
        "path": "files/ntgnIdHYuqBzIgSlbt0avClehuAtaB0cWaD7LVIi.csv",
        "extension": "csv",
        "size": 1024,
        "status": "pending",
        "updated_at": "2024-10-11T17:27:57.847000Z",
        "created_at": "2024-10-11T17:27:57.847000Z",
        "id": "6709601da7291a5441057b72",
        "download_path": "http://localhost/upload/files/ntgnIdHYuqBzIgSlbt0avClehuAtaB0cWaD7LVIi.csv"
    }
}
```

### Histórico de Upload de Arquivo
<span class="endpoint">
    <span>
        <span class="endpoint-method endpoint-get"></span> /api/v1/files/history
    </span>
    <span>
        Este endpoint permite realizar a busca de um arquivo em específico pelo nome e/ou data de criação. É obrigatório informar pelo menos um dos parâmetros.
    </span>
</span>

<br>

```js
// Passo 1: Recuperar o token retornado pelo endpoint de login
const token = localStorage.getItem('token');

// Passo 2: Montar a URL a ser chamada pelo endpoint
const url = new URL('https://localhost:8080/api/v1/files/history');
url.searchParams.append('name', 'Nome informado ou nome do arquivo'); // opcional
url.searchParams.append('created_at', '2024-10-11'); // opcional

// Passo 3: Efetuar chamada ao endpoint
const response = await fetch(url.href, {
    headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
    }
});

// Passo 4: Obter a resposta em JSON
const data = await response.json();
```

#### Resposta esperada em `data`
```json
{
    "data": {
        "name": "Nome informado ou nome do arquivo",
        "path": "files/ntgnIdHYuqBzIgSlbt0avClehuAtaB0cWaD7LVIi.csv",
        "extension": "csv",
        "size": 1024,
        "status": "pending",
        "updated_at": "2024-10-11T17:27:57.847000Z",
        "created_at": "2024-10-11T17:27:57.847000Z",
        "id": "6709601da7291a5441057b72",
        "download_path": "http://localhost/upload/files/ntgnIdHYuqBzIgSlbt0avClehuAtaB0cWaD7LVIi.csv"
    }
}
```

### Buscar Conteúdo do Arquivo
<span class="endpoint">
    <span>
        <span class="endpoint-method endpoint-post"></span> /api/v1/files/{file}
    </span>
    <span>
        Este endpoint permite buscar pelo conteúdo de um arquivo em específico por meio do ID. Caso nenhum parâmetro seja informado (TckrSymb ou RptDt) será retornado um JSON paginado para com todos os registros do arquivo. Enviando pelo menos um dos parâmetros será retornado apenas um registro.
    </span>
</span>

<br>

```js
// Passo 1: Recuperar o token retornado pelo endpoint de login
const token = localStorage.getItem('token');

// Passo 2: Recuperar o ID retornado pelo endpoint de "Histórico de Upload de Arquivo"
const fileId = '6709601da7291a5441057b72';

// Passo 3: Montar a URL a ser chamada pelo endpoint
const url = new URL(`https://localhost:8080/api/v1/files/${fileId}/content`);
url.searchParams.append('TckrSymb', '003H11'); // opcional
url.searchParams.append('RptDt', '2024-10-11'); // opcional

// Passo 4: Efetuar chamada ao endpoint
const response = await fetch(url.href, {
    headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`
    }
});

// Passo 5: Obter a resposta em JSON
const data = await response.json();
```
#### Resposta esperada em `data`

<table style="width: 100%">
<tr>
<th>Com envio de pârametros (TckrSymb e/ou RptDt)</th>
</tr>
<tr>
<td>

```json
{
    "data": {
        "RptDt": "2024-10-09",
        "TckrSymb": "003H11",
        "MktNm": "EQUITY-CASH",
        "SctyCtgyNm": "FUNDS",
        "ISIN": "BR003HCTF006",
        "CrpnNm": "KINEA CO-INVESTIMENTO FDO INV IMOB",
        "Asst": "003H",
        "AsstDesc": "003H",
        "SgmtNm": "CASH",
        "TradgStartDt": "9999-12-31",
        "TradgEndDt": "9999-12-31",
        "CFICd": "CICGRY",
        "AllcnRndLot": 1,
        "TradgCcy": "BRL",
        "DstrbtnId": 100,
        "PricFctr": 1,
        "DaysToSttlm": 2,
        "SpcfctnCd": "CI",
        "CorpActnStartDt": "9999-12-31",
        "CtdyTrtmntTpNm": "FUNGIBLE",
        "MktCptlstn": 15000,
        "FileId": "6709601da7291a5441057b72",
        "id": "6709602883b040d0000c4ad2"
    }
}
```

</td>
</tr>
</table>
<table style="width: 100%">
<tr>
<th>Sem envio de pârametros (TckrSymb e/ou RptDt)</th>
</tr>
<tr>
<td>
  
```json
{
    "data": [
        {
            "RptDt": "2024-10-09",
            "TckrSymb": "003H11",
            "MktNm": "EQUITY-CASH",
            "SctyCtgyNm": "FUNDS",
            "ISIN": "BR003HCTF006",
            "CrpnNm": "KINEA CO-INVESTIMENTO FDO INV IMOB",
            "Asst": "003H",
            "AsstDesc": "003H",
            "SgmtNm": "CASH",
            "TradgStartDt": "9999-12-31",
            "TradgEndDt": "9999-12-31",
            "CFICd": "CICGRY",
            "AllcnRndLot": 1,
            "TradgCcy": "BRL",
            "DstrbtnId": 100,
            "PricFctr": 1,
            "DaysToSttlm": 2,
            "SpcfctnCd": "CI",
            "CorpActnStartDt": "9999-12-31",
            "CtdyTrtmntTpNm": "FUNGIBLE",
            "MktCptlstn": 15000,
            "FileId": "6709601da7291a5441057b72",
            "id": "6709602883b040d0000c4ad2"
        },
        // + 9 registros
    ],
    "links": {
        "first": "http://localhost:8080/api/v1/files/6709601da7291a5441057b72/content?page=1",
        "last": "http://localhost:8080/api/v1/files/6709601da7291a5441057b72/content?page=8178",
        "prev": null,
        "next": "http://localhost:8080/api/v1/files/6709601da7291a5441057b72/content?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 8178,
        "links": [
            {
                "url": null,
                "label": "pagination.previous",
                "active": false
            },
            {
                "url": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content?page=2",
                "label": "2",
                "active": false
            },           
            {
                "url": null,
                "label": "...",
                "active": false
            },
            {
                "url": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content?page=8177",
                "label": "8177",
                "active": false
            },
            {
                "url": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content?page=8178",
                "label": "8178",
                "active": false
            },
            {
                "url": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content?page=2",
                "label": "pagination.next",
                "active": false
            }
        ],
        "path": "http://desafio.test/api/v1/files/6709601da7291a5441057b72/content",
        "per_page": 10,
        "to": 10,
        "total": 81772
    }
}
```  
</td>
</tr>
</table>
