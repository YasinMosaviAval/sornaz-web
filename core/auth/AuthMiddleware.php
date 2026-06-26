<?php

// namespace Core\Auth;

// use Core\Http\Request;

// class AuthorizeMiddleware
// {
//     public function handle(
//         Request $request,
//         callable $next
//     ) {

//         $ability =
//             $request->route()['ability']
//             ?? null;

//         if (!$ability) {
//             return $next($request);
//         }

//         if (
//             Gate::denies(
//                 $ability
//             )
//         ) {

//             http_response_code(403);

//             exit('Forbidden');
//         }

//         return $next($request);
//     }
// }



// ===============================================================================================================
// ===============================================================================================================
// ===============================================================================================================
// ===============================================================================================================


// namespace Core\Auth;

// use Core\Http\Request;

// class AuthMiddleware
// {
//     public function handle(
//         Request $request,
//         callable $next
//     ) {

//         if (
//             !auth()->check()
//         ) {

//             return redirect(
//                 '/login'
//             );
//         }

//         return $next(
//             $request
//         );
//     }
// }