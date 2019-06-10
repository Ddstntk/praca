import { Component, OnInit } from '@angular/core';
import { UsersComponentService } from "./users.component.service";
import {Router, ActivatedRoute, Params} from '@angular/router';


@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.scss'],
  providers: [UsersComponentService]
})
export class UsersComponent implements OnInit {
  user: any;
  userId: any;
  buttons = false;
  loading: boolean;
  constructor(private userService: UsersComponentService, private route: ActivatedRoute) { }

  ngOnInit() {
    this.loading = true;
    setTimeout(() => { this.loading = false }, 1000);
    this.userId = this.route.snapshot.paramMap.get('id');
    this.getProfileAction();
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
  }

  getProfileAction() {
    if(this.userId == null){
      this.userService.profileAction().subscribe(data => {
        this.user = data.userView;
        this.buttons = false;
      });
    } else {
      this.userService.userAction(this.userId).subscribe(data => {
        this.user = data.userView;
        this.buttons = true;
      });
    }
  }

  inviteAction() {

  }

  messageAction() {}
}
