# TaskFlow (Custom PHP Backend)

Um sistema de gerenciamento de tarefas inspirado no Jira e Linear, construído **do zero** em PHP puro.

O objetivo principal deste projeto não é apenas entregar uma aplicação funcional, mas demonstrar domínio profundo sobre Arquitetura de Software, Orientação a Objetos, Design Patterns e Banco de Dados. Para isso, **abri mão de usar frameworks de mercado** e construí meu próprio motor ORM e Query Builder.

## Destaques da Engenharia 

- **Custom ORM & Query Builder:** Uma engine fluente para consultas SQL complexas, inspirada no Eloquent, mas construída com PDO e Traits.
- **Eager Loading e Identity Map:** Sistema nativo para carregar sub-relações (`hasMany`, `belongsTo`) evitando duplicação de instâncias na memória e o problema de "N+1 queries".
- **Prevenção contra SQL Injection:** Uso rigoroso de `Prepared Statements` com binds dinâmicos gerados em tempo de execução.
- **Closures Recursivas:** Suporte a agrupamento de cláusulas lógicas (`whereGroup`) aninhadas.

## Como o código se parece na prática

Uma demonstração do Query Builder em ação buscando usuários, suas tarefas e comentários, com agrupamento de condições:

```php
$users = User::with('tasks.comments')
    ->whereGroup(function($query) {
        $query->where('status', 'active')
              ->orWhere('role', 'admin');
    })
    ->limit(10)
    ->get();
```
## Tecnologias Utilizadas

- Linguagem: PHP 8+ (Tipagem estrita, Traits, Closures, Reflexão)
- Banco de Dados: MySQL / PDO
- Arquitetura: MVC (Model-View-Controller) estruturado com Namespaces e Autoload (PSR-4).

## Como rodar o projeto

- php pani start
- npm run dev