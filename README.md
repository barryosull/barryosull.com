# Barry O Sullivan's Blog
Welcome one and all to the codebase for my personal site.

This site is an experiment in adapting an existing PHP markdown based blog codebase (this one => https://github.com/nekudo/shiny_blog) to suit my needs.

Basically, I've written plently of blogs, so instead I wanted to see if I could navigate someone else's code and add functionality to it. The results were mixed. The site works and I've added tests for what's important, but the existing code isn't great and is hard to navigate/modify. If I have the time, I'll rewrite the domain logic to make it simpler, remove all the abstract class noise for example. 

## TODOs
The list of things I'd like to do in this blog to make it more usable

### Done:
- Blog listings show an exert of the article
- `vendor` folder is no longer part of the repo, now uses composer properly
- Made article date optional
- Default author to me
- Use Jekyll style document headers (like dev.to)
  -  Cleaned up code 
- Code syntax should be dark background 
- Added Google Analytics
  - Disabled for development
- Updated production instance to use latest PHP7 version
    - Released site
- Added cover images to Blog article's
- Publish/Unpublish articles via meta meta info
- Published/Unpublished articles, allows sending links to unpublished articles
- Put index.php and content in a public dir (fixing security hole)
- Fixed tables in markdown parser, they weren't working right
- Fixed HTML title on blog
- Added sharing links (just twitter for now)
- Images auto resize
- View list of unpublished articles via super secret URL
- Added more articles
- Easy preview of unpublished articles
- Improved homepage
- Added links to latest article and featured library
- Single deploy script
- Made deploy script trigger after push to master
- Added some way to contact me (LinkedIn link, good enough for now)
- Use my own image hosting
    - Made it easy to upload and image and get a URL
- Moved console commands into their own folder, extracted out usecases
- Added prototype annotations with no storage
- Investigated freezes on site
    - It was apache, switched to Nginx, problem has gone away
- Resized blog title size
- Added categories to blog
- Added favicon
- Switched to HTTPs
- Converted action/responder so they return a response OBJ, instead of echoing headers/content
- Added comments section
- Made preview card for Twitter
- Made preview card for LinkedIn
- Made annotations persistent
- Added basic login for annotations feature
- Added tests for get routes (just to make sure they work)

### Next:
- Better deploy scripts, what I have currently is crap
- Upload image via web UI
- Subscribe to mailing list
- Publish automatically to dev.to
- Restructure domain code so it's layered in more sensible way
- Remove use of abstract class/inheritance anti-pattern

## License
The MIT license
