@extends('instructor.instructor_dashbord')
@section('instructor')
@php
  $id = Auth::user()->id;
  $profileData = App\Models\User::find($id);
  
@endphp
<div class="page-content">
				<div class="chat-wrapper">
					<div class="chat-sidebar">
						<div class="chat-sidebar-header">
							<div class="d-flex align-items-center">
								<div class="chat-user-online">
									<img src="{{(!empty( $profileData->photo) ? url('upload/instructor_images/'. $profileData->photo):url('upload/no_image.jpg') )}}" width="45" height="45" class="rounded-circle" alt="" />
								</div>
								<div class="flex-grow-1 ms-2">
									<p class="mb-0"> {{ $profileData->name}}</p>
								</div>
								<div class="dropdown">
									<div class="cursor-pointer font-24 dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded'></i>
									</div>
									
								</div>
							</div>
							<div class="mb-3"></div>
							<div class="input-group input-group-sm"> <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
								<input type="text" class="form-control" placeholder="People, groups, & messages"> <span class="input-group-text bg-transparent"><i class='bx bx-dialpad'></i></span>
							</div>
							
						</div>
						<div class="chat-sidebar-content">
							<div class="tab-content" id="pills-tabContent">
								<div class="tab-pane fade show active" id="pills-Chats">
									<div class="p-3">

									</div>


        <div class="chat-list">
    <div class="list-group list-group-flush">
        <a href="javascript:;" class="list-group-item">
            <div class="d-flex">
                <div class="chat-user-online">
                    <img src="{{ (!empty($question->user->photo)) ? url('upload/user_images/'.$question->user->photo) : url('upload/no_image.jpg') }}" width="42" height="42" class="rounded-circle" alt="" />

                </div>

                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0 chat-title">{{ $question->user->name }}</h6>
                    <p class="mb-0 chat-msg">Student</p>
                </div>

                <div class="chat-time">{{ \Carbon\Carbon::parse($question->created_at)->diffForHumans() }}</div>
            </div>
        </a>
    </div>
</div>

								</div>
							</div>
						</div>
					</div>
      {{-- <div class="chat-header d-flex align-items-center">
        <div class="chat-toggle-btn"><i class='bx bx-menu-alt-left'></i>
        </div>
          <h6>{{ $question['course']['course_name'] }}</h6>
        <div class="chat-top-header-menu ms-auto"> <a href="javascript:;"><i class='bx bx-video'></i></a>
          <a href="javascript:;"><i class='bx bx-phone'></i></a>
          <a href="javascript:;"><i class='bx bx-user-plus'></i></a>
        </div>
      </div> --}}

      <div class="chat-header d-flex align-items-center p-3 border-bottom bg-white">
    
    <!-- Toggle Button -->
    <button class="btn btn-link text-secondary p-0 me-3 fs-4">
        <i class='bx bx-menu-alt-left'></i>
    </button>

    <!-- Title -->
    <h6 class="mb-0 fw-semibold text-dark">
        {{ $question['course']['course_name'] }}
    </h6>

    <!-- Right Menu -->
    <div class="ms-auto d-flex align-items-center gap-3">
        <a href="#" class="text-secondary fs-4"><i class='bx bx-video'></i></a>
        <a href="#" class="text-secondary fs-4"><i class='bx bx-phone'></i></a>
        <a href="#" class="text-secondary fs-4"><i class='bx bx-user-plus'></i></a>
    </div>

</div>


					<div class="chat-content">
						<div class="chat-content-leftside">
							<div class="d-flex">
								<img src="{{ (!empty($question->user->photo)) ? url('upload/user_images/'.$question->user->photo) : url('upload/no_image.jpg') }}" width="42" height="42" class="rounded-circle" alt="" />

								<div class="flex-grow-1 ms-2">
									<p class="mb-0 chat-time">{{$question->subject}}, {{ \Carbon\Carbon::parse($question->created_at)->diffForHumans() }}</p>
									<p class="chat-left-msg">{{$question->question}}</p>
								</div>
							</div>
						</div>


            @foreach($replay as $rep)
<div class="chat-content-rightside mb-2">
    <div class="d-flex justify-content-end">
        <div class="flex-grow-1 me-2 text-end">
            <p class="mb-1 text-muted small">
                You, {{ \Carbon\Carbon::parse($rep->created_at)->diffForHumans() }}
            </p>
            <p class="bg-primary text-white p-2 rounded-3 d-inline-block chat-right-msg">
                {{ $rep->question }}
            </p>
        </div>
    </div>
</div>
@endforeach

				
					</div>
          <form action="{{ route('instructor.replay') }}" method="POST">
            @csrf 
            <input type="hidden" name="qid" value="{{ $question->id }}">
            <input type="hidden" name="course_id" value="{{ $question->course->id }}">
            <input type="hidden" name="user_id" value="{{ $question->user->id }}">
            <input type="hidden" name="instructor_id" value="{{ $profileData->id }}">
					<div class="chat-footer d-flex align-items-center">
						<div class="flex-grow-1 pe-2">
							<div class="input-group">	<span class="input-group-text"><i class='bx bx-smile'></i></span>
								<input type="text" name="question" class="form-control" placeholder="Type a message">
							</div>
						</div>
						<div class="chat-footer-menu"> 
              <button type="submit"><i class="lni lni-reply">send</i></button>
							<a href="javascript:;"><i class='bx bxs-contact'></i></a>
							<a href="javascript:;"><i class='bx bx-microphone'></i></a>
							<a href="javascript:;"><i class='bx bx-dots-horizontal-rounded'></i></a>
						</div>
					</div>
          </form>
					<!--start chat overlay-->
					<div class="overlay chat-toggle-btn-mobile"></div>
					<!--end chat overlay-->
				</div>
			</div>

@endsection