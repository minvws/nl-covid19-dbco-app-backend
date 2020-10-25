<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GGD BCO portaal - Case detail</title>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>

<div class="container-xl">

    <!-- Start of navbar component -->
    <nav class="navbar  navbar-expand-lg  navbar-light  bg-transparent  pl-0  pr-0  w-100">
        <a href="/" class="btn  btn-light  rounded-pill">
            <i class="icon  icon--arrow-left  icon--m0"></i> Terug naar Cases
        </a>

        <button class="navbar-toggler  ml-auto  bg-white"
                type="button"
                data-toggle="collapse"
                data-target="#navbarToggler"
                aria-controls="navbarToggler"
                aria-expanded="false" aria-label="Navigatie tonen">
            <span class="navbar-toggler-icon"></span>
        </button>

        @include ('identitybar')
    </nav>
    <!-- End of navbar component -->

    <!-- Start of page title component -->
    <h2 class="mt-4  mb-4  font-weight-normal">
        <span class="font-weight-bold">{{ $case->name }}</span>
    </h2>
    <!-- End of page title component -->
    <p>
        Casenr: {{ $case->caseId }}
        <br/>Eerste ziektedag: {{ $case->dateOfSymptomOnset->format('l j F') }}
    </p>

    <!-- Start of table title component -->
    <div class="d-flex  align-items-end  mb-3 mt-4">
        <h2 class="mb-0">Contacten</h2>
        <p class="mb-0  ml-auto">Velden met een <i class="icon  icon--eye"></i> zijn in de app zichtbaar voor de index</p>
    </div>
    <!-- End of table title component -->

    <!-- Start of table component -->
    <table class="table  table-rounded  table-bordered  table-has-header  table-has-footer  table-hover  table-form  table-ggd">
        <!--
            Modify the col definitions in the colgroup below to change the widths of the the columns.
            The w-* classes will be automatically generated based on the $sizes array which is defined in the scss/_variables.scss
        -->
        <colgroup>
            <col class="w-25">
            <col class="w-10">
            <col class="w-25">
            <col class="w-15">
            <col class="w-15">
            <col class="w-10">
        </colgroup>
        <thead>
        <tr>
            <th scope="col">Naam <i class="icon  icon--eye"></i></th>
            <th scope="col">Categorie <i class="icon  icon--eye"></i></th>
            <th scope="col">Context <i class="icon  icon--eye"></i></th>
            <th scope="col">Gegevens</th>
            <th scope="col">Geinformeerd</th>
            <th scope="col"></th>
        </tr>
        </thead>

        <tbody>
        @foreach ($case->tasks as $task)
        <tr>
            <th scope="row">{{ $task->label }}</th>
            <td>
                {{ $task->category }}
            </td>
            <td>
                {{ $task->context }}
            </td>
            <td>
                ...
            </td>
            <td>
                ...
            </td>
            <td class="text-center">
                >
            </td>
        </tr>
        @endforeach
        </tbody>

    </table>
    <!-- End of table component -->

</div>

<!-- Bootstrap core JavaScript -->
<!-- build:js -->
<!-- endbuild -->
</body>
</html>
