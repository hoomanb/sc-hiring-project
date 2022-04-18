<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;
use App\Models\Candidates as Candidates;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'App\Models' => __DIR__ . '/models/',
    ]
);
$loader->register();


$container = new FactoryDefault();
$container->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'db',
                'username' => 'dev',
                'password' => 'plokijuh',
                'dbname'   => 'hiring',
            ]
        );
    }
);

$app = new Micro($container);

$app->get(
    '/',
    function () {
      header('Content-type: application/json');
      echo json_encode([
        'available REST endpoints:',
        'GET /api/applicants',
        'GET /api/applicants/{id}',
        'POST /api/applicants',
      ]);
    }
);

$app->get(
  '/api/applicants',
  function () use ($app) {
    $phql = "SELECT id, name, age FROM App\Models\Candidates ORDER BY age";
    $candidates = $app
      ->modelsManager
      ->executeQuery($phql)
    ;

    $data = [];

    foreach ($candidates as $cand) {
      $data[] = [
        'type' => 'applicant',
        'id'   => $cand->id,
        'attributes' => [
        'name' => $cand->name,
        'age' => $cand->age,
      ]
      ];
    }

    header('Content-type: application/vnd.api+json'); // JSON API
    echo json_encode(['data' => $data]);
  }
);

$app->post(
  '/api/applicants',
  function () use ($app) {

    $data = json_decode($this->request->getRawBody());

    if($attributes = $data->data->attributes) {

      $candidate = new Candidates();
      $candidate->name = $attributes->name;
      $candidate->age = $attributes->age;

      $response = new Response();

      header('Content-type: application/vnd.api+json'); // JSON API
      if ( $candidate->save() ) {
        $response->setStatusCode(201, 'Created');
        $response->setJsonContent(
          [
            'data' => [
              'type' => 'applicant',
              'id' => $candidate->id,
              'attributes' => [
                'name' => $candidate->name,
                'age' => $candidate->age,
              ]
            ]
          ]
        );

        return $response;
      } else {
        $response->setStatusCode(422, 'Not modified');
        $errors = [];
        foreach ($candidate->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(
          [
              'errors' => $errors,
          ]
        );

        return $response;
      }
    }

  });

$app->handle($_SERVER['REQUEST_URI']);
