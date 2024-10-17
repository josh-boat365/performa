<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Available KPIs For You</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="badge rounded-pill bg-dark">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary">16</span>


                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"> Save</button>
                        <button class="btn btn-success"> Submit</button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#kpi-form" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">KPI Form</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#comments" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Comments</span>
                                </a>
                            </li>

                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="kpi-form" role="tabpanel">
                                <form action="#">


                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            @foreach ($kpis as $kpi)
                                                <thead>
                                                    <tr>
                                                        <th> <span class="font-size-15">Team Work <span
                                                                    class="badge rounded-pill bg-dark">80:
                                                                    {{ $kpi['id'] }}</span></span></th>
                                                        <th>Self Rating</th>
                                                        <th>Supervisor's Rating</th>
                                                        <th>Manager's Rating</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- First Row for "Communication Skills" --}}
                                                    <tr>
                                                        {{-- Header 1: Communication Skills Section --}}
                                                        <td>
                                                            <span class="font-size-13"><b>Communication Skills</b>
                                                                <span class="badge rounded-pill bg-dark">5</span>
                                                                <!-- Section Rating -->
                                                            </span>
                                                            <div class="d-flex flex-wrap gap-3 align-items-center"
                                                                style="margin-top: 1.6rem">
                                                                <div>
                                                                    <p>Good construction of the use of English </p>
                                                                </div>
                                                                <div>
                                                                    <p>Effectiveness in expressing ideas clearly</p>
                                                                </div>
                                                            </div>

                                                        </td>
                                                        {{-- Header 2: Self Rating --}}
                                                        <td>
                                                            <div class="rating-star">
                                                                <input type="hidden" class="rating-tooltip"
                                                                    data-stop="5"
                                                                    data-filled="mdi mdi-star text-primary"
                                                                    data-empty="mdi mdi-star-outline text-muted" />
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                            </div>

                                                        </td>
                                                        {{-- Header 3: Supervisor's Rating --}}
                                                        <td>
                                                            <div class="rating-star">
                                                                <input type="hidden" class="rating-tooltip"
                                                                    data-stop="5"
                                                                    data-filled="mdi mdi-star text-primary"
                                                                    data-empty="mdi mdi-star-outline text-muted" />
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                            </div>

                                                        </td>
                                                        {{-- Manager's Rating --}}
                                                        <td>
                                                            <div class="rating-star">
                                                                <input type="hidden" class="rating-tooltip"
                                                                    data-stop="5"
                                                                    data-filled="mdi mdi-star text-primary"
                                                                    data-empty="mdi mdi-star-outline text-muted" />
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                                <div class="rating-star">
                                                                    <input type="hidden" class="rating-tooltip"
                                                                        data-stop="5"
                                                                        data-filled="mdi mdi-star text-primary"
                                                                        data-empty="mdi mdi-star-outline text-muted" />
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>

                                                    {{-- Add other sections similarly --}}
                                                </tbody>
                                            @endforeach
                                        </table>
                                        <hr>
                                        {{ $kpis->links() }}
                                        <div class="d-flex gap-3 float-end">
                                            <button class="btn btn-primary">Previous</button>
                                            <button class="btn btn-success">Next</button>
                                        </div>
                                    </div>



                                </form>
                            </div>
                        </div>

                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane" id="comments" role="tabpanel">
                                <div class="card">
                                    <div class="card-body border-bottom">
                                        <div class="row">
                                            <div class="col-md-4 col-9">
                                                <h5 class="font-size-15 mb-1">Steven Franklin</h5>
                                                <p class="text-muted mb-0"><i
                                                        class="mdi mdi-circle text-success align-middle me-1"></i>
                                                    Active now</p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div>
                                            <div class="chat-conversation">
                                                <ul class="list-unstyled" data-simplebar style="max-height: 260px;">
                                                    <li>
                                                        <div class="chat-day-title">
                                                            <span class="title">Today</span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="conversation-list">
                                                            <div class="dropdown">

                                                                <a class="dropdown-toggle" href="#"
                                                                    role="button" data-bs-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#">Copy</a>
                                                                    <a class="dropdown-item" href="#">Save</a>
                                                                    <a class="dropdown-item"
                                                                        href="#">Forward</a>
                                                                    <a class="dropdown-item" href="#">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="ctext-wrap">
                                                                <div class="conversation-name">Steven Franklin</div>
                                                                <p>
                                                                    Hello!
                                                                </p>
                                                                <p class="chat-time mb-0"><i
                                                                        class="bx bx-time-five align-middle me-1"></i>
                                                                    10:00</p>
                                                            </div>

                                                        </div>
                                                    </li>

                                                    <li class="right">
                                                        <div class="conversation-list">
                                                            <div class="dropdown">

                                                                <a class="dropdown-toggle" href="#"
                                                                    role="button" data-bs-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#">Copy</a>
                                                                    <a class="dropdown-item" href="#">Save</a>
                                                                    <a class="dropdown-item"
                                                                        href="#">Forward</a>
                                                                    <a class="dropdown-item" href="#">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="ctext-wrap">
                                                                <div class="conversation-name">Henry Wells</div>
                                                                <p>
                                                                    Hi, How are you? What about our next meeting?
                                                                </p>

                                                                <p class="chat-time mb-0"><i
                                                                        class="bx bx-time-five align-middle me-1"></i>
                                                                    10:02</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="conversation-list">
                                                            <div class="dropdown">

                                                                <a class="dropdown-toggle" href="#"
                                                                    role="button" data-bs-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#">Copy</a>
                                                                    <a class="dropdown-item" href="#">Save</a>
                                                                    <a class="dropdown-item"
                                                                        href="#">Forward</a>
                                                                    <a class="dropdown-item" href="#">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="ctext-wrap">
                                                                <div class="conversation-name">Steven Franklin</div>
                                                                <p>
                                                                    Yeah everything is fine
                                                                </p>

                                                                <p class="chat-time mb-0"><i
                                                                        class="bx bx-time-five align-middle me-1"></i>
                                                                    10:06</p>
                                                            </div>

                                                        </div>
                                                    </li>

                                                    <li class="last-chat">
                                                        <div class="conversation-list">
                                                            <div class="dropdown">

                                                                <a class="dropdown-toggle" href="#"
                                                                    role="button" data-bs-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#">Copy</a>
                                                                    <a class="dropdown-item" href="#">Save</a>
                                                                    <a class="dropdown-item"
                                                                        href="#">Forward</a>
                                                                    <a class="dropdown-item" href="#">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="ctext-wrap">
                                                                <div class="conversation-name">Steven Franklin</div>
                                                                <p>& Next meeting tomorrow 10.00AM</p>
                                                                <p class="chat-time mb-0"><i
                                                                        class="bx bx-time-five align-middle me-1"></i>
                                                                    10:06</p>
                                                            </div>

                                                        </div>
                                                    </li>

                                                    <li class="right">
                                                        <div class="conversation-list">
                                                            <div class="dropdown">

                                                                <a class="dropdown-toggle" href="#"
                                                                    role="button" data-bs-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#">Copy</a>
                                                                    <a class="dropdown-item" href="#">Save</a>
                                                                    <a class="dropdown-item"
                                                                        href="#">Forward</a>
                                                                    <a class="dropdown-item" href="#">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="ctext-wrap">
                                                                <div class="conversation-name">Henry Wells</div>
                                                                <p>
                                                                    Wow that's great
                                                                </p>

                                                                <p class="chat-time mb-0"><i
                                                                        class="bx bx-time-five align-middle me-1"></i>
                                                                    10:07</p>
                                                            </div>
                                                        </div>
                                                    </li>


                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="p-3 chat-input-section">
                                        <div class="row">
                                            <div class="col">
                                                <div class="position-relative">
                                                    <input type="text" class="form-control rounded chat-input"
                                                        placeholder="Enter Message...">
                                                    <div class="chat-input-links">
                                                        <ul class="list-inline mb-0">
                                                            <li class="list-inline-item"><a
                                                                    href="javascript: void(0);"><i
                                                                        class="mdi mdi-emoticon-happy-outline"></i></a>
                                                            </li>
                                                            <li class="list-inline-item"><a
                                                                    href="javascript: void(0);"><i
                                                                        class="mdi mdi-file-image-outline"></i></a>
                                                            </li>
                                                            <li class="list-inline-item"><a
                                                                    href="javascript: void(0);"><i
                                                                        class="mdi mdi-file-document-outline"></i></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit"
                                                    class="btn btn-primary chat-send w-md waves-effect waves-light"><span
                                                        class="d-none d-sm-inline-block me-2">Send</span> <i
                                                        class="mdi mdi-send"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>


    </div>

</x-base-layout>
