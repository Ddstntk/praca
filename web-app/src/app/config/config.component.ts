import { Component, OnInit } from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
import {ConfigComponentService} from "./config.component.service";


@Component({
  selector: 'app-config',
  templateUrl: './config.component.html',
  styleUrls: ['./config.component.scss'],
  // providers: [UsersComponentService]
})
export class ConfigComponent implements OnInit {
  chatWth: any;
  chatHgt: any;
  chatCheck: any;

  postsWth: any;
  postsHgt: any;
  postsCheck: any;

  usersWth: any;
  usersHgt: any;
  usersCheck: any;

  friendsWth: any;
  friendsHgt: any;
  friendsCheck: any;

  config: any;
  constructor(private configService: ConfigComponentService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit() {
    this.configService.getConfigAction().subscribe( data => {
      var jsonData = data.userDashboard.Dashboard.replace('/\"','/')
      this.config = JSON.parse(jsonData);
      console.log(JSON.stringify(this.config))
      this.chatWth = parseFloat(this.config.chat.wdt);
      this.chatHgt = parseFloat(this.config.chat.hgt);

      this.postsWth = parseFloat(this.config.posts.wdt);
      this.postsHgt = parseFloat(this.config.posts.hgt);

      this.usersWth = parseFloat(this.config.users.wdt);
      this.usersHgt = parseFloat(this.config.users.hgt);

      this.friendsWth = parseFloat(this.config.friends.wdt);
      this.friendsHgt = parseFloat(this.config.friends.hgt);
      // console.log(this.friendsWth, this.friendsHgt, this.chatHgt, this.chatWth, this.postsHgt, this.postsWth, this.usersHgt, this.usersWth)
        this.chatCheck = this.chatWth > 0 && this.chatHgt > 0;
        this.postsCheck = this.postsWth > 0 && this.postsHgt > 0;
        this.usersCheck = this.usersWth > 0 && this.usersHgt > 0;
        this.friendsCheck = this.friendsWth > 0 && this.friendsHgt > 0;

    })

  }

  applyChanges() {
    this.configService.setConfigAction(
      {
        chat:
          {
            wdt: this.chatWth,
            hgt: this.chatHgt
          },
        posts:
          {
            wdt: this.postsWth,
            hgt: this.postsHgt
          },
        users:
          {
            wdt: this.usersWth,
            hgt: this.usersHgt
          },
        friends:
          {
            wdt: this.friendsWth,
            hgt: this.friendsHgt
          }
      }
    ).subscribe(data=>{
        // this.router.navigate(['/dashboard']);
    })

  }

  checkSize(){

      if(this.chatHgt + this.friendsHgt > 2){
          this.chatHgt = 1;
          this.friendsHgt = 1;
      }

      if(this.postsHgt + this.usersHgt > 2){
          this.postsHgt = 1;
          this.usersHgt = 1;
      }

      if(this.chatWth + this.postsWth > 4){
          this.chatWth = 2;
          this.postsWth = 2;
      }

      if(this.friendsWth + this.usersWth > 4){
          this.friendsWth = 2;
          this.usersWth = 2;
      }

      if(!this.chatCheck){
          this.chatWth = 0;
          this.chatHgt = 0;
      }
      if(!this.postsCheck){
          this.postsWth = 0;
          this.postsHgt = 0;
      }
      if(!this.usersCheck){
          this.usersWth = 0;
          this.usersHgt = 0;
      }
      if(!this.friendsCheck){
          this.friendsWth = 0;
          this.friendsHgt = 0;
      }
  }

}
