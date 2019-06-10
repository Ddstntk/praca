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

  order = [];
  config: any;
  layoutScheme: any;
  componentChoice = [];
  options = ["chat", "posty", "znajomi", "uzytkownicy"]
    componentsToLoad = [];
  colorMap = [
                [],
                []
            ]
  constructor(private configService: ConfigComponentService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit() {
    this.configService.getConfigAction().subscribe( data => {
      var jsonData = data.userDashboard.Dashboard.replace('/\"','/')
      this.config = JSON.parse(jsonData);
      console.log(JSON.stringify(this.config))
        this.order = this.config.components;
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
        components: this.componentsToLoad,
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
  changeColors(){

      // if(this.colorMap[0].length + this.chatWth < 4){
      //     for(let i = 0; i < this.chatWth; i++){
      //         this.colorMap[0].push("chat")
      //     }
      //     if (this.chatHgt > 1){
      //         for(let k = 0; k < this.chatHgt; k++){
      //             this.colorMap[1].push("chat")
      //         }
      //     }
      // } else {
      //     for(let i = 0; i < this.chatWth; i++){
      //         this.colorMap[1].push("chat")
      //     }
      // }
      // if(this.chatWth > 1 && this.postsWth > 1){
      //     this.colorMap[1].push("none")
      // }
      // if(this.colorMap[0].length + this.postsWth < 4){
      //     for(let i = 0; i < this.postsWth; i++){
      //         this.colorMap[0].push("posts")
      //     }
      //     if (this.postsHgt > 1){
      //         for(let k = 0; k < this.postsHgt; k++){
      //             this.colorMap[1].push("posts")
      //         }
      //     }
      // } else {
      //     for(let i = 0; i < this.chatWth; i++){
      //         this.colorMap[1].push("posts")
      //     }
      // }

      console.log(this.colorMap)
  }
  checkSize(){
      //
      // if(this.chatHgt + this.friendsHgt > 2){
      //     this.chatHgt = 1;
      //     this.friendsHgt = 1;
      // }
      //
      // if(this.postsHgt + this.usersHgt > 2){
      //     this.postsHgt = 1;
      //     this.usersHgt = 1;
      // }
      //
      // if(this.chatWth + this.postsWth > 4){
      //     this.chatWth = 2;
      //     this.postsWth = 2;
      // }
      //
      // if(this.friendsWth + this.usersWth > 4){
      //     this.friendsWth = 2;
      //     this.usersWth = 2;
      // }
      //
      // if(!this.chatCheck){
      //     this.chatWth = 0;
      //     this.chatHgt = 0;
      // }
      // if(!this.postsCheck){
      //     this.postsWth = 0;
      //     this.postsHgt = 0;
      // }
      // if(!this.usersCheck){
      //     this.usersWth = 0;
      //     this.usersHgt = 0;
      // }
      // if(!this.friendsCheck){
      //     this.friendsWth = 0;
      //     this.friendsHgt = 0;
      // }
      // this.changeColors();
  }

  changeLayout(layout){
      this.layoutScheme = layout;
      this.componentChoice = [];
      for(let i = 0; i < layout; i++){
          this.componentChoice.push(i+1)
      }
      for(let i = 0; i < layout; i++){
          this.componentsToLoad[i] = "chat"
      }
      // this.componentsToLoad = [...this.componentChoice];
  }

  assignModule(comp, opt){
      // this.allToZero();
      this.componentsToLoad[comp-1] = opt;
    console.log(comp);
    console.log(opt);
    console.log(this.componentsToLoad);
      if(this.layoutScheme == 1){
          this.componentsToLoad.forEach((element) => {
              if(element == "chat"){
                  this.chatWth = 4;
                  this.chatHgt = 2;
              } else if (element == "posty"){
                  this.postsWth = 4;
                  this.postsHgt = 2;
              } else if (element == "znajomi"){
                  this.friendsWth = 4;
                  this.friendsHgt = 2;
              } else if (element == "uzytkownicy"){
                  this.usersWth = 4;
                  this.usersHgt = 2;
              }
          })
      }
      if(this.layoutScheme == 2){
          this.componentsToLoad.forEach((element) => {
              if(element == "chat"){
                  this.chatWth = 2;
                  this.chatHgt = 2;
              } else if (element == "posty"){
                  this.postsWth = 2;
                  this.postsHgt = 2;
              } else if (element == "znajomi"){
                  this.friendsWth = 2;
                  this.friendsHgt = 2;
              } else if (element == "uzytkownicy"){
                  this.usersWth = 2;
                  this.usersHgt = 2;
              }
          })
      }
      if(this.layoutScheme == 3){
          this.componentsToLoad.forEach((element, index) => {
              if(index == 0){
                  if(element == "chat"){
                      this.chatWth = 2;
                      this.chatHgt = 2;
                  } else if (element == "posty"){
                      this.postsWth = 2;
                      this.postsHgt = 2;
                  } else if (element == "znajomi"){
                      this.friendsWth = 2;
                      this.friendsHgt = 2;
                  } else if (element == "uzytkownicy"){
                      this.usersWth = 2;
                      this.usersHgt = 2;
                  }
              }
              else {
                  if (element == "chat") {
                      this.chatWth = 2;
                      this.chatHgt = 1;
                  } else if (element == "posty") {
                      this.postsWth = 2;
                      this.postsHgt = 1;
                  } else if (element == "znajomi") {
                      this.friendsWth = 2;
                      this.friendsHgt = 1;
                  } else if (element == "uzytkownicy") {
                      this.usersWth = 2;
                      this.usersHgt = 1;
                  }
              }
          })
      }
      if(this.layoutScheme == 4){
          this.componentsToLoad.forEach((element) => {
              if(element == "chat"){
                  this.chatWth = 2;
                  this.chatHgt = 1;
              } else if (element == "posty"){
                  this.postsWth = 2;
                  this.postsHgt = 1;
              } else if (element == "znajomi"){
                  this.friendsWth = 2;
                  this.friendsHgt = 1;
              } else if (element == "uzytkownicy"){
                  this.usersWth = 2;
                  this.usersHgt = 1;
              }
          })
      }
  }
  allToZero(){
      this.chatHgt = 0;
      this.chatWth = 0;
      this.postsWth = 0;
      this.postsHgt = 0;
      this.friendsWth = 0;
      this.friendsHgt = 0;
      this.usersWth = 0;
      this.usersHgt = 0;
  }
}
