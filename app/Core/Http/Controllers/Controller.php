<?php

namespace App\Core\Http\Controllers;

use App\Core\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\OpenApi(
 *     x={
 *         "tagGroups"={
 *             {
 *                 "name"="Core",
 *                 "tags"={"Module", "Resource"}
 *             },
 *             {
 *                 "name"="Security",
 *                 "tags"={"Authentication", "Permission", "Role", "User"}
 *             },
 *             {
 *                 "name"="Partner",
 *                 "tags"={"PartnerType"}
 *             }
 *         }
 *     }
 * )
 * @OA\Info(
 *      title="Simplify ERP API",
 *      version="1.0.0",
 *      description="A sessão abaixo contém a documentação da API utilizada para o Simplify ERP.",
 *      @OA\Contact(
 *          email="felipe.allware@gmail.com",
 *          name="Felipe Silva"
 *      ),
 *      @OA\License(
 *          name="Licença MIT",
 *          url="https://github.com/felipesilva15/simplify-erp-api/blob/main/LICENSE"
 *      )
 * )
 * @OA\Server(
 *     description="Local",
 *     url="http://localhost:8000"
 * )
 * @OA\Server(
 *     description="Sandbox",
 *     url="https://sandbox.simplify-erp.felipesilva15.com.br"
 * )
 * @OA\Server(
 *     description="Production",
 *     url="https://api.simplify-erp.felipesilva15.com.br"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;
}
