 <!-- BEGIN: Header-->
 <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
     <div class="navbar-wrapper">
         <div class="navbar-container content">
             <div class="navbar-collapse" id="navbar-mobile">
                 <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                     <ul class="nav navbar-nav">
                         <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                     class="ficon feather icon-menu"></i></a></li>
                         @if (Auth::user()->level == 'salesman' && !request()->is(['home']))
                             <li class="nav-item d-xl-none mr-auto"><a class="nav-link hidden-xs" href="#" id="refresh"><i
                                         class="ficon feather icon-refresh-cw"></i></a></li>
                         @endif

                     </ul>

                 </div>
                 @if (Auth::user()->level != 'salesman')
                     <ul class="nav navbar-nav float-right">

                         @if (Auth::user()->level == 'direktur')
                             <li class="dropdown dropdown-notification nav-item">
                                 <a class="nav-link nav-link-label" href="/lembur">
                                     <i class="ficon" data-feather="watch"></i>
                                     <span class="badge badge-pill badge-danger badge-up mr-2">{{ $jmlajuanlembur }}</span>
                                 </a>
                             </li>
                         @endif


                         <li class="dropdown dropdown-notification nav-item">
                             <a class="nav-link nav-link-label" href="/penilaiankaryawan/10/MP/list">
                                 <i class="ficon" data-feather="file-text"></i>
                                 <span class="badge badge-pill badge-danger badge-up mr-2">{{ $jmlpenilaiankar }}</span>
                             </a>
                         </li>

                         <li class="dropdown dropdown-notification nav-item">
                             <a class="nav-link nav-link-label" href="/ticket">
                                 <i class="ficon" data-feather="tool"></i>
                                 @if ($level == 'manager accounting' || $level == 'rom')
                                     <span class="badge badge-pill badge-warning badge-up mr-1">{{ $ticket_pending_gm }}</span>
                                 @elseif($level == 'direktur')
                                     <span class="badge badge-pill badge-info badge-up mr-1">{{ $ticket_pending_dirut }}</span>
                                 @elseif($level == 'direktur')
                                     <span class="badge badge-pill badge-info badge-up mr-1">{{ $ticket_pending_admin }}</span>
                                 @else
                                     <span class="badge badge-pill badge-warning badge-up mr-1">{{ $ticket_pending }}</span>
                                 @endif
                             </a>
                         </li>
                         <li class="dropdown dropdown-notification nav-item">
                             <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                                 <i class="ficon feather icon-book"></i>
                                 <span class="badge badge-pill badge-danger badge-up">{{ $memo_unread }}</span>
                             </a>
                             <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                 <li class="dropdown-menu-header bg-danger">
                                     <div class="dropdown-header m-0 p-2">
                                         <h3 class="white">{{ $memo_unread != null ? $memo_unread : 0 }} New</h3><span
                                             class="notification-title">Unread New Regulation</span>
                                     </div>
                                 </li>
                                 <li class="scrollable-container media-list ps">
                                     @if ($memo_data != null)
                                         @foreach ($memo_data as $d)
                                             <a href="" class="d-flex justify-content-between">
                                                 <div class="media d-flex align-item-start">
                                                     <div class="media-left">
                                                         <i class="feather icon-book font-medium-5 info"></i>
                                                     </div>
                                                     <div class="media-body">
                                                         <h6>{{ ucwords(strtolower($d->judul_memo)) }}</h6>
                                                         <small>{{ $d->no_memo }} - {{ $d->kategori }} -
                                                             {{ $d->name }}</small>
                                                     </div>
                                                     <small>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $d->tanggal)->diffForHumans() }}</small>
                                                 </div>
                                             </a>
                                         @endforeach
                                     @endif
                                 </li>
                             </ul>
                         </li>
                         {{-- <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up">5</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <div class="dropdown-header m-0 p-2">
                                    <h3 class="white">5 New</h3><span class="notification-title">App Notifications</span>
                                </div>
                            </li>

                        </ul>
                    </li> --}}
                         <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                 data-toggle="dropdown">
                                 <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{ Auth::user()->name }}</span><span
                                         class="user-status">
                                         {{-- @if (Auth::user()->id == 176 || Auth::user()->id == 164 || Auth::user()->id == 185)
                                     SPV HRD
                                     @else
                                     {{ ucwords(Auth::user()->level) }}
                                     @endif --}}
                                         {{ ucwords(Auth::user()->level) }}
                                     </span></div><span>
                                     @if (!empty(Auth::user()->foto))
                                         @php
                                             $path = Storage::url('users/' . Auth::user()->foto);
                                         @endphp


                                         <img class="round" src="{{ url($path) }}" alt="avatar" height="40" width="40">
                                     @else
                                         <img class="round" src="{{ asset('app-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="avatar"
                                             height="40" width="40">
                                     @endif
                                 </span>
                             </a>
                             <div class="dropdown-menu dropdown-menu-right">
                                 <a class="dropdown-item" href="page-user-profile.html">

                                 </a>
                                 <div class="dropdown-divider"></div>
                                 <form action="/postlogout" method="POST">
                                     @csrf
                                     <button type="submit" class="dropdown-item"><i class="feather icon-power"></i>
                                         Logout</button>
                                 </form>
                             </div>
                         </li>
                     @else
                         <ul class="nav navbar-nav float-right">
                             @if (Cookie::get('kodepelanggan') != null)
                                 <li class="dropdown dropdown-notification nav-item">
                                     <a class="btn btn-sm btn-primary mt-1" href="/pelanggan/getpelanggan">
                                         <i class="ficon" data-feather="users" style="font-size: 12px"></i> Pelanggan
                                     </a>
                                 </li>
                             @endif

                             <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                     data-toggle="dropdown">
                                     <div class="user-nav d-sm-flex d-none"><span
                                             class="user-name text-bold-600">{{ Auth::user()->name }}</span><span class="user-status">
                                             {{ ucwords(Auth::user()->level) }}
                                         </span></div><span>
                                         @if (!empty(Auth::user()->foto))
                                             @php
                                                 $path = Storage::url('users/' . Auth::user()->foto);
                                             @endphp


                                             <img class="round" src="{{ url($path) }}" alt="avatar" height="40" width="40">
                                         @else
                                             <img class="round" src="{{ asset('app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                                 alt="avatar" height="40" width="40">
                                         @endif
                                     </span>
                                 </a>
                                 <div class="dropdown-menu dropdown-menu-right">
                                     <a class="dropdown-item" href="page-user-profile.html">

                                     </a>
                                     <div class="dropdown-divider"></div>
                                     <form action="/postlogout" method="POST">
                                         @csrf
                                         <button type="submit" class="dropdown-item"><i class="feather icon-power"></i>
                                             Logout</button>
                                     </form>
                                 </div>
                             </li>
                         </ul>
                 @endif

             </div>
         </div>
     </div>
 </nav>
 <ul class="main-search-list-defaultlist d-none">
     <li class="d-flex align-items-center"><a class="pb-25" href="#">
             <h6 class="text-primary mb-0">Files</h6>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100"
             href="#">
             <div class="d-flex">
                 <div class="mr-50"><img src="{{ asset('app-assets/images/icons/xls.png') }}" alt="png" height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing
                         Manager</small>
                 </div>
             </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100"
             href="#">
             <div class="d-flex">
                 <div class="mr-50"><img src="{{ asset('app-assets/images/icons/jpg.png') }}" alt="png" height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd
                         Developer</small>
                 </div>
             </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100"
             href="#">
             <div class="d-flex">
                 <div class="mr-50"><img src="{{ asset('app-assets/images/icons/pdf.png') }}" alt="png" height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital
                         Marketing Manager</small>
                 </div>
             </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100"
             href="#">
             <div class="d-flex">
                 <div class="mr-50"><img src="{{ asset('app-assets/images/icons/doc.png') }}" alt="png" height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web
                         Designer</small>
                 </div>
             </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
         </a></li>
     <li class="d-flex align-items-center"><a class="pb-25" href="#">
             <h6 class="text-primary mb-0">Members</h6>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100"
             href="#">
             <div class="d-flex align-items-center">
                 <div class="avatar mr-50"><img src="{{ asset('app-assets/images/portrait/small/avatar-s-8.jpg') }}" alt="png"
                         height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">{{ Auth::user()->name }}</p><small class="text-muted">UI
                         designer</small>
                 </div>
             </div>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100"
             href="#">
             <div class="d-flex align-items-center">
                 <div class="avatar mr-50"><img src="{{ asset('app-assets/images/portrait/small/avatar-s-1.jpg') }}" alt="png"
                         height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd
                         Developer</small>
                 </div>
             </div>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100"
             href="#">
             <div class="d-flex align-items-center">
                 {{-- <div class="avatar mr-50"><img src="{{asset('app-assets/images/portrait/small/avatar-s-14.jpg" alt="png')}}" height="32"></div> --}}
                 <div class="search-data">
                     <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing
                         Manager</small>
                 </div>
             </div>
         </a></li>
     <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100"
             href="#">
             <div class="d-flex align-items-center">
                 <div class="avatar mr-50"><img src="{{ asset('app-assets/images/portrait/small/avatar-s-6.jpg') }}" alt="png"
                         height="32"></div>
                 <div class="search-data">
                     <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                 </div>
             </div>
         </a></li>
 </ul>
 <ul class="main-search-list-defaultlist-other-list d-none">
     <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a
             class="d-flex align-items-center justify-content-between w-100 py-50">
             <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No
                     results found.</span></div>
         </a></li>
 </ul>
 <!-- END: Header-->
